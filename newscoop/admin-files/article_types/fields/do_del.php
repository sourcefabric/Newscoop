<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('DeleteArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to delete article type fields.", array(), 'article_type_fields'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$fieldName = Input::Get('f_field_name');


$field = new ArticleTypeField($articleTypeName, $fieldName);
if ($field->exists()) {
	$field->delete();
    
    $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('article_type');
}
camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
?>