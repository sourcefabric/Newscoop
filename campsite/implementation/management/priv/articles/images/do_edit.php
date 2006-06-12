<?php
camp_load_translation_strings("article_images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_image_template_id = Input::Get('f_image_template_id', 'int', null, true);
$f_image_description = trim(Input::Get('f_image_description', 'string', null, true));
$f_image_photographer = trim(Input::Get('f_image_photographer', 'string', null, true));
$f_image_place = trim(Input::Get('f_image_place', 'string', null, true));
$f_image_date = Input::Get('f_image_date', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

$articleObj =& new Article($f_language_selected, $f_article_number);

if (!$g_user->hasPermission('ChangeImage') && !$g_user->hasPermission('AttachImageToArticle')) {
	$ref = camp_html_article_url($articleObj, $f_language_id, 'edit.php');
	header("Location: $ref");
}


$imageObj =& new Image($f_image_id);

if (!is_null($f_image_description) && $g_user->hasPermission('ChangeImage')) {
	$attributes = array();
	$attributes['Description'] = $f_image_description;
	$attributes['Photographer'] = $f_image_photographer;
	$attributes['Place'] = $f_image_place;
	$attributes['Date'] = $f_image_date;
	if ($g_user->hasPermission('ChangeImage')) {
		$imageObj->update($attributes);
	}
}

if ($g_user->hasPermission('AttachImageToArticle')) {
	if (is_numeric($f_image_template_id) && ($f_image_template_id > 0)) {
		ArticleImage::SetTemplateId($f_article_number, $f_image_id, $f_image_template_id);
	}
}

$ref = camp_html_article_url($articleObj, $f_language_id, 'edit.php');
header("Location: $ref");

?>