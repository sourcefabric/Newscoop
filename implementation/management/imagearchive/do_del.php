<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files();
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/imagearchive/include.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/priv/CampsiteInterface.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header('Location: /priv/logout.php');
	exit;
}
$ImageId = isset($_REQUEST['image_id'])?$_REQUEST['image_id']:0;
if (!is_numeric($ImageId) || ($ImageId <= 0)) {
	header('Location: '.CAMPSITE_IMAGEARCHIVE_DIR.'index.php?'.Image_GetSearchUrl($_REQUEST));
	exit;	
}

$imageObj =& new Image($ImageId);

// This file can only be accessed if the user has the right to delete images.
if (!$User->hasPermission('DeleteImage')) {
	header('Location: /priv/logout.php');
	exit;		
}
if ($imageObj->inUse()) {
	header('Location: '.CAMPSITE_IMAGEARCHIVE_DIR.'index.php?'.Image_GetSearchUrl($_REQUEST));
	exit;
}

$imageObj->delete();

$logtext = getGS('Image $1 deleted', $imageObj->getImageId()); 
Log::Message($logtext, $User->getUserName(), 42);

// Fix image offset if we just deleted all the images shown on the current page.
$imageSearch =& new ImageSearch($_REQUEST);
$imageSearch->run();
if ($_REQUEST['image_offset'] >= $imageSearch->getNumImagesFound()) {
	$_REQUEST['image_offset'] = $_REQUEST['image_offset']-$imageSearch->getImagesPerPage();
}
if ($_REQUEST['image_offset'] < 0) {
	$_REQUEST['image_offset'] = 0;
}

// Go back to article image list.
header('Location: '.CAMPSITE_IMAGEARCHIVE_DIR.'index.php?'.Image_GetSearchUrl($_REQUEST));
exit;
?>