<?php
camp_load_translation_strings("article_files");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Attachment.php');

if (!$g_user->hasPermission('ChangeFile')) {
	camp_html_display_error(getGS('You do not have the right to change files.' ), null, true);
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);
$f_description = trim(Input::Get('f_description', 'string', '', true));
$f_language_specific = Input::Get('f_language_specific');
$f_content_disposition = Input::Get('f_content_disposition');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$articleObj->userCanModify($g_user)) {
	camp_html_display_error(getGS('You do not have the right to change the article.'), null, true);
	exit;
}

$attachmentObj =& new Attachment($f_attachment_id);
$attachmentObj->setDescription($f_language_selected, $f_description);
if ($f_language_specific == "yes") {
	$attachmentObj->setLanguageId($f_language_selected);
} else {
	$attachmentObj->setLanguageId(null);
}
if ($f_content_disposition == "attachment" || empty($f_content_disposition)) {
	$attachmentObj->setContentDisposition($f_content_disposition);
}

$ref = camp_html_article_url($articleObj, $f_language_id, 'edit.php');

// Go back to article.
header("Location: $ref");

?>