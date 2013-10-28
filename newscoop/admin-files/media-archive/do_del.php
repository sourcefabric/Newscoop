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

$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid() || ($f_image_id <= 0)) {
	camp_html_goto_page("/$ADMIN/media-archive/index.php");
}

$imageObj = new Image($f_image_id);

// This file can only be accessed if the user has the right to delete images.
if (!$g_user->hasPermission('DeleteImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
if ($imageObj->inUse()) {
	camp_html_add_msg($translator->trans("Image is in use, it cannot be deleted.", array(), 'media_archive'));
	camp_html_goto_page("/$ADMIN/media-archive/index.php");
}

$imageDescription = $imageObj->getDescription();
$result = $imageObj->delete();
if (PEAR::isError($result)) {
	camp_html_add_msg($result->getMessage());
} else {
	// Go back to article image list.
	camp_html_add_msg($translator->trans("Image $1 deleted.", array('$1' => $imageDescription), 'media_archive'), "ok");
}
camp_html_goto_page("/$ADMIN/media-archive/index.php");

?>
