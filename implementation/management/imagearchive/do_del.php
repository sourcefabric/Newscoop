<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/imagearchive");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/imagearchive/include.inc.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$ImageId = Input::Get('image_id', 'int', 0);
$view = Input::Get('view', 'string', 'thumbnail', true);

$imageNav =& new ImageNav(CAMPSITE_IMAGEARCHIVE_IMAGES_PER_PAGE, $view);
if (!Input::IsValid() || ($ImageId <= 0)) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;	
}

$imageObj =& new Image($ImageId);

// This file can only be accessed if the user has the right to delete images.
if (!$User->hasPermission('DeleteImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}
if ($imageObj->inUse()) {
	header('Location: index.php?'.$imageNav->getSearchLink());
	exit;
}

$imageObj->delete();

$logtext = getGS('Image $1 deleted', $imageObj->getImageId()); 
Log::Message($logtext, $User->getUserName(), 42);

// Go back to article image list.
header('Location: index.php?'.$imageNav->getSearchLink());
exit;
?>