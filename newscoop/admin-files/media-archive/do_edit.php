<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// check input
$f_image_id = Input::Get('f_image_id', 'int', 0);
$f_image_description = Input::Get('f_image_description');
$f_image_photographer = Input::Get('f_image_photographer');
$f_image_place = Input::Get('f_image_place');
$f_image_date = Input::Get('f_image_date');
$f_image_status = Input::Get('f_image_status');
//$f_image_url = Input::Get('f_image_url', 'string', '', true);
if (!Input::IsValid() || ($f_image_id <= 0)) {
	camp_html_goto_page("/$ADMIN/media-archive/index.php");
}

$imageObj = new Image($f_image_id);

if (!$g_user->hasPermission('ChangeImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}

$updateArray = array('Description' => $f_image_description,
					'Photographer' => $f_image_photographer,
					'Place' => $f_image_place,
					'Date' => $f_image_date,
					'Status' => $f_image_status,
                    'photographer_url' => Input::Get('f_photographer_url'));
//if (!empty($f_image_url)) {
//	$updateArray['URL'] = $f_image_url;
//}
$imageObj->update($updateArray);

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('image');

camp_html_add_msg($translator->trans("Image updated.", array(), 'media_archive'), "ok");
camp_html_goto_page("/$ADMIN/media-archive/edit.php?f_image_id=$f_image_id");
?>
