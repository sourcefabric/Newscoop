<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/imagearchive/include.inc.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
// Check input 
if (!isset($_REQUEST['cDescription']) || !isset($_REQUEST['cPhotographer'])
	|| !isset($_REQUEST['cPlace']) || !isset($_REQUEST['cDate'])) {
	header('Location: '.CAMPSITE_IMAGEARCHIVE_DIR.'index.php?'.Image_GetSearchUrl($_REQUEST));
	exit;	
}
if (empty($_REQUEST['cURL']) && !isset($_FILES['cImage'])) {
	header('Location: '.CAMPSITE_IMAGEARCHIVE_DIR.'index.php?'.Image_GetSearchUrl($_REQUEST));
	exit;	
}
if (!$User->hasPermission('AddImage')) {
	header('Location: /priv/logout.php');
	exit;	
}
$view = isset($_REQUEST['view'])?$_REQUEST['view']:'thumbnail';
$attributes = array();
$attributes['Description'] = $_REQUEST['cDescription'];
$attributes['Photographer'] = $_REQUEST['cPhotographer'];
$attributes['Place'] = $_REQUEST['cPlace'];
$attributes['Date'] = $_REQUEST['cDate'];
if (!empty($_REQUEST['cURL'])) {
	$image =& Image::OnAddRemoteImage($_REQUEST['cURL'], $attributes);
}
else {
	$image =& Image::OnImageUpload($_FILES['cImage'], $attributes);
}

$logtext = getGS('The image $1 has been added.', $attributes['Description']);
Log::Message($logtext, $User->getUserName(), 41);

// Go back to article image list.
header('Location: '.CAMPSITE_IMAGEARCHIVE_DIR.'index.php?'.Image_GetSearchUrl($_REQUEST));
exit;
?>