<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_files");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Attachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Translation.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddFile')) {
	camp_html_display_error(getGS('You do not have the right to add files.'), null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_description = Input::Get('f_description');
$f_language_specific = Input::Get('f_language_specific');
$f_content_disposition = Input::Get('f_content_disposition');

$BackLink = Input::Get('BackLink', 'string', null, true);

if (!isset($_FILES["f_file"]) || (filesize($_FILES["f_file"]['tmp_name']) == false)) {
	camp_html_display_error(getGS("You must select a file to upload."), null, true);
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

$description =& new Translation($f_language_selected);
$description->create($f_description);

$attributes = array();
$attributes['fk_description_id'] = $description->getPhraseId();
$attributes['fk_user_id'] = $User->getUserId();
if ($f_language_specific == "yes") {
	$attributes['fk_language_id'] = $f_language_selected;
}
if ($f_content_disposition == "attachment") {
	$attributes['content_disposition'] = "attachment";
}

if (!empty($_FILES['f_file'])) {
	$file = Attachment::OnFileUpload($_FILES['f_file'], $attributes);
} else {
	header('Location: '.camp_html_article_url($articleObj, $f_language_id, 'files/popup.php'));
	exit;
}

// Check if image was added successfully
if (!is_object($file)) {
	if ($file == -1) 
		camp_html_display_error("File upload failed because your files permission are not writable by the webuser.", $BackLink, true);
	else
	    camp_html_display_error("File upload failed.", $BackLink, true);
	exit;
}

ArticleAttachment::AddFileToArticle($file->getAttachmentId(), $articleObj->getArticleNumber());

?>
<script>
window.opener.document.forms.article_edit.onsubmit();
window.opener.document.forms.article_edit.submit();
window.close();
</script>
