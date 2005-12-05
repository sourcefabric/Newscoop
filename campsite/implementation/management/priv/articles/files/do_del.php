<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_files");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Attachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);

// Check input
if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);

if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."));
	exit;
}

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$articleObj->userCanModify($User)) {
	camp_html_display_error(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users."));
	exit;		
}

$attachmentObj =& new Attachment($f_attachment_id);
if (!$attachmentObj->exists()) {
	camp_html_display_error(getGS('Attachment does not exist.'));
	exit;
}
ArticleAttachment::RemoveAttachmentFromArticle($f_attachment_id, $f_article_number);
$attachmentObj->delete();

// Go back to article.
header('Location: '.camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
exit;
?>