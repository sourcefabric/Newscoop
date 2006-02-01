<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid() || ($f_image_id <= 0)) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;	
}

$imageObj =& new Image($f_image_id);

// This file can only be accessed if the user has the right to delete images.
if (!$User->hasPermission('DeleteImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}
if ($imageObj->inUse()) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}

$imageObj->delete();

// Go back to article image list.
header("Location: /$ADMIN/imagearchive/index.php");
exit;
?>