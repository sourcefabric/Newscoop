<?php
camp_load_translation_strings("article_types");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

$articleTypeName = Input::Get('f_article_type');
$articleTypeFieldName = Input::Get('f_field_name');
$status = Input::Get('f_status');
$errorMsgs = array();

$articleTypeField = new ArticleTypeField($articleTypeName, $articleTypeFieldName);
$articleTypeField->setStatus($status);

camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));

?>