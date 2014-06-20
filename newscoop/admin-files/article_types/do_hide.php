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

$articleTypeName = Input::Get('f_article_type');
$status = Input::Get('f_status');
$errorMsgs = array();

$articleType = new ArticleType($articleTypeName);
$articleType->setStatus($status);

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('article_type');

\Zend_Registry::get('container')->getService('dispatcher')
	->dispatch('article_type.hide', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
	    'article_type' => $articleType
	)));

camp_html_goto_page("/$ADMIN/article_types/");