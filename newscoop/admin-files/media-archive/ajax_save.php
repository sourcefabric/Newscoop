<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$f_image_id = Input::Get('f_image_id');
$f_field = Input::Get('f_field');
$f_value = Input::Get('f_value');

$imageObj = new Image($f_image_id);

if (!$g_user->hasPermission('ChangeImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}

$fieldNames = array('description' => 'Description', 'photographer' => 'Photographer', 'place' => 'Place', 'date' => 'Date', 'status' => 'Status');

$updateArray = array($fieldNames[$f_field] => $f_value);

$imageObj->update($updateArray);