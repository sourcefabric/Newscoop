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

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to rename article types.", array(), 'article_types'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$errorMsgs = array();

$articleType = new ArticleType($articleTypeName);
if ($articleType->exists()) {
    $articleType->setCommentsEnabled(!$articleType->commentsEnabled());

    $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('article_type');

    \Zend_Registry::get('container')->getService('dispatcher')
        ->dispatch('article_type.comments_management', new \Newscoop\EventDispatcher\Events\GenericEvent($this, array(
            'article_type' => $articleType,
            'new_status' => !$articleType->commentsEnabled()
        )));
}
camp_html_goto_page("/$ADMIN/article_types/");

?>