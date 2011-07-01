<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

if (!Saas::singleton()->hasPermission('ManageArticleTypes')) {
    camp_html_display_error(getGS("You do not have the right to delete article types."));
    exit;
}

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$articleTypeName = Input::Get('f_article_type');
$articleTypeFieldName = Input::Get('f_field_name');
$isContent = Input::Get('f_is_content');
$errorMsgs = array();

$articleTypeField = new ArticleTypeField($articleTypeName, $articleTypeFieldName);
$articleTypeField->setIsContent($isContent == 'true');

camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));

?>