<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/imagearchive/include.inc.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
// Check input 
$cDescription = Input::Get('cDescription');
$cPhotographer = Input::Get('cPhotographer');
$cPlace = Input::Get('cPlace');
$cDate = Input::Get('cDate');
$cURL = Input::Get('cURL', 'string', '', true);
$view = Input::Get('view', 'string', 'thumbnail', true);
$BackLink = Input::Get('BackLink', 'string', null, true);

$imageNav =& new ImageNav(CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
$imageNav->clearSearchStrings();
$imageNav->setProperty('order_by', 'time_created');

if (!Input::IsValid()) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}
if (empty($cURL) && !isset($_FILES['cImage'])) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}
if (!$User->hasPermission('AddImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}
$attributes = array();
$attributes['Description'] = $cDescription;
$attributes['Photographer'] = $cPhotographer;
$attributes['Place'] = $cPlace;
$attributes['Date'] = $cDate;
if (!empty($cURL)) {
	$image =& Image::OnAddRemoteImage($cURL, $attributes, $User->getId());
}
elseif (!empty($_FILES['cImage'])) {
	$image =& Image::OnImageUpload($_FILES['cImage'], $attributes, $User->getId());
}
else {
	header('Location: '.camp_html_display_error(getGS("You must select an image file to upload."), $BackLink));
	exit;
}

// Check if image was added successfully
if (!is_object($image)) {
	header('Location: '.camp_html_display_error($image, $BackLink));
	exit;	
}

$logtext = getGS('The image $1 has been added.', $attributes['Description']);
Log::Message($logtext, $User->getUserName(), 41);

// Go back to article image list.
header('Location: index.php?'.$imageNav->getSearchLink());
exit;
?>