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

// check input
$f_image_id = Input::Get('f_image_id', 'int', 0);
$f_image_description = Input::Get('f_image_description');
$f_image_photographer = Input::Get('f_image_photographer');
$f_image_place = Input::Get('f_image_place');
$f_image_date = Input::Get('f_image_date');
$f_image_url = Input::Get('f_image_url', 'string', '', true);
if (!Input::IsValid() || ($f_image_id <= 0)) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;	
}

$imageObj =& new Image($f_image_id);

if (!$User->hasPermission('ChangeImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}

$updateArray = array('Description' => $f_image_description,
					'Photographer' => $f_image_photographer,
					'Place' => $f_image_place,
					'Date' => $f_image_date);
if (!empty($f_image_url)) {
	$updateArray['URL'] = $f_image_url;
}
$imageObj->update($updateArray);

header("Location: /$ADMIN/imagearchive/edit.php?f_image_id=$f_image_id");
exit;
?>