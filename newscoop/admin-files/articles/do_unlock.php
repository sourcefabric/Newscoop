<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())));
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);

// If the user does not have permission to change the article
// or they didnt create the article, give them the boot.
if (!$articleObj->userCanModify($g_user)) {
	camp_html_display_error($translator->trans("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users.", array(), 'articles'));
	exit;
}

$articleObj->setIsLocked(false);

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('article');

camp_html_add_msg($translator->trans("Article unlocked.", array(), 'articles'), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&Unlock=true"));

?>