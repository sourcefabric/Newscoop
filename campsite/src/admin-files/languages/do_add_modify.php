<?php
camp_load_translation_strings("languages");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/localizer/Localizer.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/TimeUnit.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageLanguages')) {
	camp_html_display_error(getGS("You do not have the right to add new languages."));
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$editMode = ($f_language_id != 0);
$f_language_name = Input::Get('f_language_name');
$f_native_name = Input::Get('f_native_name');
$f_language_code = Input::Get('f_language_code');
$f_month_1 = Input::Get('f_month_1', 'string', '', true);
$f_month_2 = Input::Get('f_month_2', 'string', '', true);
$f_month_3 = Input::Get('f_month_3', 'string', '', true);
$f_month_4 = Input::Get('f_month_4', 'string', '', true);
$f_month_5 = Input::Get('f_month_5', 'string', '', true);
$f_month_6 = Input::Get('f_month_6', 'string', '', true);
$f_month_7 = Input::Get('f_month_7', 'string', '', true);
$f_month_8 = Input::Get('f_month_8', 'string', '', true);
$f_month_9 = Input::Get('f_month_9', 'string', '', true);
$f_month_10 = Input::Get('f_month_10', 'string', '', true);
$f_month_11 = Input::Get('f_month_11', 'string', '', true);
$f_month_12 = Input::Get('f_month_12', 'string', '', true);
$f_short_month_1 = Input::Get('f_short_month_1', 'string', '', true);
$f_short_month_2 = Input::Get('f_short_month_2', 'string', '', true);
$f_short_month_3 = Input::Get('f_short_month_3', 'string', '', true);
$f_short_month_4 = Input::Get('f_short_month_4', 'string', '', true);
$f_short_month_5 = Input::Get('f_short_month_5', 'string', '', true);
$f_short_month_6 = Input::Get('f_short_month_6', 'string', '', true);
$f_short_month_7 = Input::Get('f_short_month_7', 'string', '', true);
$f_short_month_8 = Input::Get('f_short_month_8', 'string', '', true);
$f_short_month_9 = Input::Get('f_short_month_9', 'string', '', true);
$f_short_month_10 = Input::Get('f_short_month_10', 'string', '', true);
$f_short_month_11 = Input::Get('f_short_month_11', 'string', '', true);
$f_short_month_12 = Input::Get('f_short_month_12', 'string', '', true);
$f_sunday = Input::Get('f_sunday', 'string', '', true);
$f_monday = Input::Get('f_monday', 'string', '', true);
$f_tuesday = Input::Get('f_tuesday', 'string', '', true);
$f_wednesday = Input::Get('f_wednesday', 'string', '', true);
$f_thursday = Input::Get('f_thursday', 'string', '', true);
$f_friday = Input::Get('f_friday', 'string', '', true);
$f_saturday = Input::Get('f_saturday', 'string', '', true);
$f_short_sunday = Input::Get('f_short_sunday', 'string', '', true);
$f_short_monday = Input::Get('f_short_monday', 'string', '', true);
$f_short_tuesday = Input::Get('f_short_tuesday', 'string', '', true);
$f_short_wednesday = Input::Get('f_short_wednesday', 'string', '', true);
$f_short_thursday = Input::Get('f_short_thursday', 'string', '', true);
$f_short_friday = Input::Get('f_short_friday', 'string', '', true);
$f_short_saturday = Input::Get('f_short_saturday', 'string', '', true);
$D = Input::Get('D', 'string', '', true);
$W = Input::Get('W', 'string', '', true);
$M = Input::Get('M', 'string', '', true);
$Y = Input::Get('Y', 'string', '', true);

$correct = 1;
$created = 0;
if (($f_language_name == "") || ($f_native_name == "") || ($f_language_code == "") ) {
    $correct = 0;
}
$errorMsgs = array();
if ($f_language_name == "") {
    camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
}
if ($f_native_name == "") {
   	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Native name').'</B>'));
}
if ($f_language_code == "") {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Code').'</B>'));
}

if ($editMode) {
    $languageObj = new Language($f_language_id);
}

if ($correct) {
	$columns = array('Name' => $f_language_name,
					 'Code' => $f_language_code,
					 'OrigName' => $f_native_name,
					 'Month1' => $f_month_1,
					 'Month2' => $f_month_2,
					 'Month3' => $f_month_3,
					 'Month4' => $f_month_4,
					 'Month5' => $f_month_5,
					 'Month6' => $f_month_6,
					 'Month7' => $f_month_7,
					 'Month8' => $f_month_8,
					 'Month9' => $f_month_9,
					 'Month10' => $f_month_10,
					 'Month11' => $f_month_11,
					 'Month12' => $f_month_12,
					 'ShortMonth1' => $f_short_month_1,
					 'ShortMonth2' => $f_short_month_2,
					 'ShortMonth3' => $f_short_month_3,
					 'ShortMonth4' => $f_short_month_4,
					 'ShortMonth5' => $f_short_month_5,
					 'ShortMonth6' => $f_short_month_6,
					 'ShortMonth7' => $f_short_month_7,
					 'ShortMonth8' => $f_short_month_8,
					 'ShortMonth9' => $f_short_month_9,
					 'ShortMonth10' => $f_short_month_10,
					 'ShortMonth11' => $f_short_month_11,
					 'ShortMonth12' => $f_short_month_12,
					 'WDay1' => $f_sunday,
					 'WDay2' => $f_monday,
					 'WDay3' => $f_tuesday,
					 'WDay4' => $f_wednesday,
					 'WDay5' => $f_thursday,
					 'WDay6' => $f_friday,
			                 'WDay7' => $f_saturday,
					 'ShortWDay1' => $f_short_sunday,
					 'ShortWDay2' => $f_short_monday,
					 'ShortWDay3' => $f_short_tuesday,
					 'ShortWDay4' => $f_short_wednesday,
					 'ShortWDay5' => $f_short_thursday,
					 'ShortWDay6' => $f_short_friday,
					 'ShortWDay7' => $f_short_saturday);

	$success = true;
    if ($editMode) {
		$languageObj->update($columns);
    } else {
    	$languageObj = new Language();
    	$result = $languageObj->create($columns);
    	if (PEAR::isError($result)) {
    		camp_html_add_msg($result->getMessage());
    		$success = false;
    	} else {
    		$f_language_id = $languageObj->getLanguageId();
    	}
    }
    if ($success) {
		TimeUnit::SetTimeUnit('D', $f_language_id, $D);
		TimeUnit::SetTimeUnit('W', $f_language_id, $W);
		TimeUnit::SetTimeUnit('M', $f_language_id, $M);
		TimeUnit::SetTimeUnit('Y', $f_language_id, $Y);
	    camp_html_goto_page("/$ADMIN/languages/index.php");
    }
}
$link = "/$ADMIN/languages/add_modify.php". ($editMode ? "?f_language_id=".$f_language_id : "");
camp_html_goto_page($link);

?>