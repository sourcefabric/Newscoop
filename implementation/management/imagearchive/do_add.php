<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/imagearchive/include.inc.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
// Check input 
$imageNav =& new ImageNav($_REQUEST, CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $_REQUEST['view']);
if (!isset($_REQUEST['cDescription']) || !isset($_REQUEST['cPhotographer'])
	|| !isset($_REQUEST['cPlace']) || !isset($_REQUEST['cDate'])) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}
if (empty($_REQUEST['cURL']) && !isset($_FILES['cImage'])) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}
if (!$User->hasPermission('AddImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}
$view = isset($_REQUEST['view'])?$_REQUEST['view']:'thumbnail';
$attributes = array();
$attributes['Description'] = $_REQUEST['cDescription'];
$attributes['Photographer'] = $_REQUEST['cPhotographer'];
$attributes['Place'] = $_REQUEST['cPlace'];
$attributes['Date'] = $_REQUEST['cDate'];
if (!empty($_REQUEST['cURL'])) {
	$image =& Image::OnAddRemoteImage($_REQUEST['cURL'], $attributes, $User->getId());
}
else {
	$image =& Image::OnImageUpload($_FILES['cImage'], $attributes, $User->getId());
}

$logtext = getGS('The image $1 has been added.', $attributes['Description']);
Log::Message($logtext, $User->getUserName(), 41);

// Go back to article image list.
header('Location: index.php?'.$imageNav->getSearchLink());
exit;
?>