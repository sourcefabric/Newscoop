<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

$f_articleTypeName = Input::Get('f_article_type');
$f_filter = Input::Get('f_filter');
$errorMsgs = array();

$res = \ArticleType::SetTypeFilter($f_articleTypeName, $f_filter);

camp_html_goto_page("/$ADMIN/article_types/");
?>