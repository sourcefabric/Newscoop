<?php
camp_load_translation_strings("imagearchive");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid() || ($f_image_id <= 0)) {
	camp_html_goto_page("/$ADMIN/imagearchive/index.php");
}

$imageObj = new Image($f_image_id);

// This file can only be accessed if the user has the right to delete images.
if (!$g_user->hasPermission('DeleteImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
if ($imageObj->inUse()) {
	camp_html_add_msg(getGS("Image is in use, it cannot be deleted."));
	camp_html_goto_page("/$ADMIN/imagearchive/index.php");
}

$imageDescription = $imageObj->getDescription();
$result = $imageObj->delete();
if (PEAR::isError($result)) {
	camp_html_add_msg($result->getMessage());
} else {
	// Go back to article image list.
	camp_html_add_msg(getGS("Image '$1' deleted.", $imageDescription), "ok");
}
camp_html_goto_page("/$ADMIN/imagearchive/index.php");

?>