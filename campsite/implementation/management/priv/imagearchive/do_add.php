<?php
camp_load_translation_strings("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

// Check input
$f_image_description = Input::Get('f_image_description');
$f_image_photographer = Input::Get('f_image_photographer');
$f_image_place = Input::Get('f_image_place');
$f_image_date = Input::Get('f_image_date');
$f_image_url = Input::Get('f_image_url', 'string', '', true);

if (!Input::IsValid()) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}
if (empty($f_image_url) && !isset($_FILES['f_image_file'])) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}
if (!$g_user->hasPermission('AddImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$attributes = array();
$attributes['Description'] = $f_image_description;
$attributes['Photographer'] = $f_image_photographer;
$attributes['Place'] = $f_image_place;
$attributes['Date'] = $f_image_date;
if (!empty($f_image_url)) {
	$image = Image::OnAddRemoteImage($f_image_url, $attributes, $g_user->getUserId());
}
elseif (!empty($_FILES['f_image_file'])) {
	$image = Image::OnImageUpload($_FILES['f_image_file'], $attributes, $g_user->getUserId());
}
else {
	camp_html_display_error(getGS("You must select an image file to upload."), "/$ADMIN/imagearchive/add.php");
	exit;
}

// Check if image was added successfully
if (!is_object($image)) {
	camp_html_display_error($image, "/$ADMIN/imagearchive/add.php");
}

header("Location: /$ADMIN/imagearchive/edit.php?f_image_id=".$image->getImageId());
exit;
?>