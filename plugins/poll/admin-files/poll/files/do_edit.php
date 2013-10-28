<?php
$translator = \Zend_Registry::get('container')->getService('translator');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ChangeFile')) {
	camp_html_display_error($translator->trans('You do not have the right to change files.' ), null, true);
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
	camp_html_display_error($translator->trans('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

if (!$articleObj->exists()) {
	camp_html_display_error($translator->trans("Article does not exist."), null, true);
	exit;
}

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$articleObj->userCanModify($g_user)) {
	camp_html_display_error($translator->trans('You do not have the right to change the article.', array(), 'plugin_poll'), null, true);
	exit;
}

$attachmentObj = new Attachment($f_attachment_id);
$attachmentObj->setDescription($f_language_selected, $f_description);
if ($f_language_specific == "yes") {
	$attachmentObj->setLanguageId($f_language_selected);
} else {
	$attachmentObj->setLanguageId(null);
}
if ($f_content_disposition == "attachment" || empty($f_content_disposition)) {
	$attachmentObj->setContentDisposition($f_content_disposition);
}

// Go back to article.
camp_html_add_msg($translator->trans("File $1 updated.", array('$1' => $attachmentObj->getFileName()), 'plugin_poll'), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));

?>