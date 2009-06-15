<?php
camp_load_translation_strings("article_images");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);
$f_image_template_id = Input::Get('f_image_template_id', 'int', 0);

// Check input
if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$g_user->hasPermission('AttachImageToArticle')) {
	camp_html_display_error(getGS("You do not have the right to attach images to articles."), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);
$imageObj = new Image($f_image_id);

ArticleImage::RemoveImageFromArticle($f_image_id, $f_article_number, $f_image_template_id);

camp_html_add_msg(getGS('The image "$1" has been removed from the article.', $imageObj->getDescription()), "ok");
camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
?>