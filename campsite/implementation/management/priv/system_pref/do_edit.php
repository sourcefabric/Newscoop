<?php
camp_load_translation_strings("system_pref");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SystemPref.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

// Check permissions
if (!$g_user->hasPermission('ChangeSystemPreferences')) {
	camp_html_display_error(getGS("You do not have the right to change system preferences."));
	exit;
}

$f_keyword_separator = Input::Get('f_keyword_separator');
$f_login_num = Input::Get('f_login_num', 'int');
$f_max_upload_filesize = Input::Get('f_max_upload_filesize');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$max_upload_filesize_bytes = camp_convert_bytes($f_max_upload_filesize);
SystemPref::Set("KeywordSeparator", $f_keyword_separator);
if ($f_login_num >= 0) {
	SystemPref::Set("LoginFailedAttemptsNum", $f_login_num);
}
if ($max_upload_filesize_bytes > 0 &&
	$max_upload_filesize_bytes <= camp_convert_bytes(ini_get('upload_max_filesize'))) {
	SystemPref::Set("MaxUploadFileSize", $f_max_upload_filesize);
} else {
	camp_html_add_msg(getGS('Invalid Max Upload File Size value submitted'));
}
camp_html_add_msg(getGS("System preferences updated."), "ok");

camp_html_goto_page("/$ADMIN/system_pref/");
?>
