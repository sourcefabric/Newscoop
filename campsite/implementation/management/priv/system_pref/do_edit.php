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
$f_db_repl_host = Input::Get('f_db_repl_host');
$f_db_repl_user = Input::Get('f_db_repl_user');
$f_db_repl_pass = Input::Get('f_db_repl_pass');
$f_db_repl_port = intval(Input::Get('f_db_repl_port'));
$f_cc_hostname = Input::Get('f_cc_hostname');
$f_cc_hostport = intval(Input::Get('f_cc_hostport'));
$f_cc_xrpcpath = Input::Get('f_cc_xrpcpath');
$f_cc_xrpcfile = Input::Get('f_cc_xrpcfile');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$msg_ok = 1;
$max_upload_filesize_bytes = camp_convert_bytes($f_max_upload_filesize);
// Keyword Separator
SystemPref::Set("KeywordSeparator", $f_keyword_separator);
if ($f_login_num >= 0) {
	SystemPref::Set("LoginFailedAttemptsNum", $f_login_num);
}
// Max Upload File Size
if ($max_upload_filesize_bytes > 0 &&
		$max_upload_filesize_bytes <= camp_convert_bytes(ini_get('upload_max_filesize'))) {
	SystemPref::Set("MaxUploadFileSize", $f_max_upload_filesize);
} else {
	$msg_ok = 0;
	camp_html_add_msg(getGS('Invalid Max Upload File Size value submitted'));
}

// Database Replication Host, User and Password
if (!empty($f_db_repl_host) && !empty($f_db_repl_user) && !empty($f_db_repl_pass)) {
	SystemPref::Set("DBReplicationHost", $f_db_repl_host);
	SystemPref::Set("DBReplicationUser", $f_db_repl_user);
	SystemPref::Set("DBReplicationPass", $f_db_repl_pass);
} else {
	$msg_ok = 0;
	camp_html_add_msg(getGS("Database Replication data incomplete"));
}
// Database Replication Port
if (empty($f_db_repl_port) || !is_int($f_db_repl_port)) {
        $f_db_repl_port = 3306;
}
SystemPref::Set("DBReplicationPort", $f_db_repl_port);

// Campcaster Server
SystemPref::Set("CampcasterHostName", $f_cc_hostname);
SystemPref::Set("CampcasterHostPort", $f_cc_hostport);
SystemPref::Set("CampcasterXRPCPath", $f_cc_xrpcpath);
SystemPref::Set("CampcasterXRPCFile", $f_cc_xrpcfile);

$logtext = getGS('System preferences updated');
Log::Message($logtext, $g_user->getUserId(), 171);

// Success message if everything was ok
if ($msg_ok == 1) {
	camp_html_add_msg(getGS("System preferences updated."), "ok");
}
camp_html_goto_page("/$ADMIN/system_pref/");
?>
