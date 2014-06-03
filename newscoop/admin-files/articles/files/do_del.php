<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('DeleteFile')) {
	camp_html_display_error($translator->trans('You do not have the right to delete files.', array(), 'article_files'), null, true);
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);

// Check input
if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), null, true);
	exit;
}

if (!$g_user->hasPermission("DeleteFile")) {
	camp_html_display_error($translator->trans("You do not have the right to delete file attachments.", array(), 'article_files'), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);

if (!$articleObj->exists()) {
	camp_html_display_error($translator->trans("Article does not exist."), null, true);
	exit;
}

$attachmentObj = new Attachment($f_attachment_id);
if (!$attachmentObj->exists()) {
	camp_html_display_error($translator->trans('Attachment does not exist.', array(), 'article_files'), null, true);
	exit;
}
$filePath = dirname($attachmentObj->getStorageLocation()) . '/' . $attachmentObj->getFileName();
ArticleAttachment::RemoveAttachmentFromArticle($f_attachment_id, $f_article_number);
$logtext = $translator->trans('File #$1 "$2" unattached', array(
		 '$1' => $attachmentObj->getAttachmentId(), '$2' => $attachmentObj->getFileName()), 'article_files');
Log::ArticleMessage($articleObj, $logtext, null, 39);

$attachmentFileName = $attachmentObj->getFileName();
$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('attachments');

// Go back to article.
camp_html_add_msg($translator->trans("File $1 unattached.", array('$1' => $attachmentFileName), 'article_files'), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
?>
