<?php
camp_load_translation_strings("article_files");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Attachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (!$g_user->hasPermission('DeleteFile')) {
	camp_html_display_error(getGS('You do not have the right to delete files.' ), null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);

// Check input
if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

if (!$g_user->hasPermission("DeleteFile")) {
	camp_html_display_error(getGS("You do not have the right to delete file attachments."), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);

if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

$attachmentObj = new Attachment($f_attachment_id);
if (!$attachmentObj->exists()) {
	camp_html_display_error(getGS('Attachment does not exist.'), null, true);
	exit;
}
$filePath = dirname($attachmentObj->getStorageLocation()) . '/' . $attachmentObj->getFileName();
if (!is_writable(dirname($filePath))) {
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $filePath,
			basename($attachmentObj->getStorageLocation())));
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
	exit;
}
ArticleAttachment::RemoveAttachmentFromArticle($f_attachment_id, $f_article_number);
$attachmentObj->delete();

// Go back to article.
camp_html_add_msg(getGS("File '$1' deleted.", $attachmentObj->getFileName()), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
?>