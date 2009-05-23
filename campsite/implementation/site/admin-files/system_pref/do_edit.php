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

$f_campsite_online = Input::Get('f_campsite_online');
$f_site_title = Input::Get('f_site_title');
$f_site_metakeywords = Input::Get('f_site_metakeywords');
$f_site_metadescription = Input::Get('f_site_metadescription');
$f_cache_enabled = Input::Get('f_cache_enabled');
$f_cache_engine = Input::Get('f_cache_engine');
$f_secret_key = Input::Get('f_secret_key');
$f_session_lifetime = intval(Input::Get('f_session_lifetime'));
$f_keyword_separator = Input::Get('f_keyword_separator');
$f_login_num = Input::Get('f_login_num', 'int');
$f_max_upload_filesize = Input::Get('f_max_upload_filesize');
$f_smtp_host = Input::Get('f_smtp_host');
$f_smtp_port = intval(Input::Get('f_smtp_port'));
$f_use_replication = Input::Get('f_use_replication');
$f_db_repl_host = Input::Get('f_db_repl_host');
$f_db_repl_user = Input::Get('f_db_repl_user');
$f_db_repl_pass = Input::Get('f_db_repl_pass');
$f_db_repl_port = intval(Input::Get('f_db_repl_port'));
$f_use_campcaster = Input::Get('f_use_campcaster');
$f_cc_hostname = Input::Get('f_cc_hostname');
$f_cc_hostport = intval(Input::Get('f_cc_hostport'));
$f_cc_xrpcpath = Input::Get('f_cc_xrpcpath');
$f_cc_xrpcfile = Input::Get('f_cc_xrpcfile');
$f_external_subs_management = Input::Get('f_external_subs_management');
if ($f_external_subs_management != 'Y' && $f_external_subs_management != 'N') {
	$f_external_subs_management = SystemPref::Get('ExternalSubscriptionManagement');
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$msg_ok = 1;

// Site On-line
SystemPref::Set('SiteOnline', $f_campsite_online);

// Site title
SystemPref::Set('SiteTitle', $f_site_title);

// Site Meta Keywords
SystemPref::Set('SiteMetaKeywords', $f_site_metakeywords);

// Site Meta Description
SystemPref::Set('SiteMetaDescription', $f_site_metadescription);

// Caching
SystemPref::Set('CacheEngine', $f_cache_engine);
if ($f_cache_enabled == 'Y') {
    if (CampCache::IsSupported($f_cache_engine)) {
        SystemPref::Set('SiteCacheEnabled', $f_cache_enabled);
    } else {
        $msg_ok = 0;
        camp_html_add_msg(getGS('Invalid: You need PHP $1 enabled in order to use the caching system.', $f_cache_engine));
    }
} else {
    SystemPref::Set('SiteCacheEnabled', $f_cache_enabled);
}

// Secret key
SystemPref::Set('SiteSecretKey', $f_secret_key);

// Session life time
SystemPref::Set('SiteSessionLifeTime', $f_session_lifetime);

// Keyword Separator
SystemPref::Set("KeywordSeparator", $f_keyword_separator);

// Number of failed login attempts
if ($f_login_num >= 0) {
	SystemPref::Set("LoginFailedAttemptsNum", $f_login_num);
}

// Max Upload File Size
$max_upload_filesize_bytes = camp_convert_bytes($f_max_upload_filesize);
if ($max_upload_filesize_bytes > 0 &&
		$max_upload_filesize_bytes <= camp_convert_bytes(ini_get('upload_max_filesize'))) {
	SystemPref::Set("MaxUploadFileSize", $f_max_upload_filesize);
} else {
	$msg_ok = 0;
	camp_html_add_msg(getGS('Invalid Max Upload File Size value submitted'));
}

// SMTP Host/Port
if (empty($f_smtp_host)) {
    $f_smtp_host = 'localhost';
}
SystemPref::Set('SMTPHost', $f_smtp_host);
if ($f_smtp_port <= 0) {
    $f_smtp_port = 25;
}
SystemPref::Set('SMTPPort', $f_smtp_port);

// External subscription management
SystemPref::Set('ExternalSubscriptionManagement', $f_external_subs_management);

// Replication
if ($f_use_replication == 'Y') {
    // Database Replication Host, User and Password
    if (!empty($f_db_repl_host) && !empty($f_db_repl_user)) {
        SystemPref::Set("DBReplicationHost", $f_db_repl_host);
        SystemPref::Set("DBReplicationUser", $f_db_repl_user);
        SystemPref::Set("DBReplicationPass", $f_db_repl_pass);
        SystemPref::Set("UseDBReplication", $f_use_replication);
    } else {
        $msg_ok = 0;
        camp_html_add_msg(getGS("Database Replication data incomplete"));
    }
    // Database Replication Port
    if (empty($f_db_repl_port) || !is_int($f_db_repl_port)) {
        $f_db_repl_port = 3306;
    }
    SystemPref::Set("DBReplicationPort", $f_db_repl_port);
} else {
    SystemPref::Set("UseDBReplication", 'N');
}

// Campcaster integrity
if ($f_use_campcaster == 'Y') {
    // Campcaster Server
    SystemPref::Set("CampcasterHostName", $f_cc_hostname);
    SystemPref::Set("CampcasterHostPort", $f_cc_hostport);
    SystemPref::Set("CampcasterXRPCPath", $f_cc_xrpcpath);
    SystemPref::Set("CampcasterXRPCFile", $f_cc_xrpcfile);
    SystemPref::Set("UseCampcasterAudioclips", $f_use_campcaster);
} else {
    SystemPref::Set("UseCampcasterAudioclips", 'N');
}

$logtext = getGS('System preferences updated');
Log::Message($logtext, $g_user->getUserId(), 171);

// Success message if everything was ok
if ($msg_ok == 1) {
	camp_html_add_msg(getGS("System preferences updated."), "ok");
}

CampPlugin::PluginAdminHooks(__FILE__);

camp_html_goto_page("/$ADMIN/system_pref/");
?>
