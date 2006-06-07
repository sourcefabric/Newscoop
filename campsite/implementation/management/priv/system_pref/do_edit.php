<?php
camp_load_translation_strings("system_pref");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SystemPref.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

// Check permissions
if (!$g_user->hasPermission('ChangeSystemPreferences')) {
	camp_html_display_error(getGS("You do not have the right to change system preferences."));
	exit;
}

$f_keyword_separator = Input::Get('f_keyword_separator');

if (!empty($f_keyword_separator)) {
	SystemPref::Set("KeywordSeparator", $f_keyword_separator);
}

$f_login_num = Input::Get('f_login_num');

if (!empty($f_login_num)) {
	SystemPref::Set("FailedAttemptsNum", $f_login_num);
}

header("Location: /$ADMIN/system_pref/");
exit;
?>