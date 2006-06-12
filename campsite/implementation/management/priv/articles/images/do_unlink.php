<?php
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

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

$articleObj =& new Article($f_language_selected, $f_article_number);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$g_user->hasPermission('AttachImageToArticle')) {
	camp_html_display_error(getGS("You do not have the right to attach images to articles."), null, true);
	exit;
}

ArticleImage::RemoveImageFromArticle($f_image_id, $f_article_number, $f_image_template_id);

// Go back to article image list.
header('Location: '.camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
exit;
?>