<?php
camp_load_translation_strings("system_pref");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ChangeSystemPreferences')) {
    camp_html_display_error(getGS("You do not have the right to change system preferences."));
    exit;
}

$f_campsite_online = Input::Get('f_campsite_online');
$f_site_title = strip_tags(Input::Get('f_site_title'));
$f_site_metakeywords = strip_tags(Input::Get('f_site_metakeywords'));
$f_site_metadescription = strip_tags(Input::Get('f_site_metadescription'));
$f_time_zone = Input::Get('f_time_zone');
$f_cache_engine = Input::Get('f_cache_engine');
$f_template_cache_handler = Input::Get('f_template_cache_handler');
$f_secret_key = strip_tags(Input::Get('f_secret_key'));
$f_session_lifetime = Input::Get('f_session_lifetime', 'int');
$f_imagecache_lifetime = Input::Get('f_imagecache_lifetime', 'int');
$f_keyword_separator = strip_tags(Input::Get('f_keyword_separator'));
$f_login_num = Input::Get('f_login_num', 'int');
$f_max_upload_filesize = strip_tags(Input::Get('f_max_upload_filesize'));
$f_smtp_host = strip_tags(Input::Get('f_smtp_host'));
$f_smtp_port = Input::Get('f_smtp_port', 'int');
$f_collect_statistics = Input::Get('f_collect_statistics');
$f_editor_image_ratio = Input::Get('f_editor_image_ratio', 'int', null, true);
$f_editor_image_width = Input::Get('f_editor_image_width', 'int', null, true);
$f_editor_image_height = Input::Get('f_editor_image_height', 'int', null, true);
$f_editor_image_zoom = Input::Get('f_editor_image_zoom');
$f_use_replication = Input::Get('f_use_replication');
$f_db_repl_host = strip_tags(Input::Get('f_db_repl_host'));
$f_db_repl_user = strip_tags(Input::Get('f_db_repl_user'));
$f_db_repl_pass = strip_tags(Input::Get('f_db_repl_pass'));
$f_db_repl_port = Input::Get('f_db_repl_port', 'int');
$f_external_subs_management = Input::Get('f_external_subs_management');
$f_password_recovery = Input::Get('f_password_recovery');
$f_password_recovery_from = Input::Get('f_password_recovery_from');
if ($f_external_subs_management != 'Y' && $f_external_subs_management != 'N') {
    $f_external_subs_management = SystemPref::Get('ExternalSubscriptionManagement');
}
$f_template_filter = Input::Get('f_template_filter', '', 'string', true);
$f_external_cron_management = Input::Get('f_external_cron_management');
if ($f_external_cron_management != 'Y' && $f_external_cron_management != 'N') {
    $f_external_cron_management = SystemPref::Get('ExternalCronManagement');
}
if ($f_external_cron_management == 'N'
        && !is_readable(CS_INSTALL_DIR.DIR_SEP.'cron_jobs'.DIR_SEP.'all_at_once')) {
    $f_external_cron_management = 'Y';
}

// geolocation
$f_geo = array(
    'map_center_latitude_default' => Input::Get('f_map_center_latitude_default', 'float'),
    'map_center_longitude_default' => Input::Get('f_map_center_longitude_default', 'float'),
    'map_display_resolution_default' => Input::Get('f_map_display_resolution_default', 'int'),
    'map_view_width_default' => Input::Get('f_map_view_width_default', 'int', 600, true),
    'map_view_height_default' => Input::Get('f_map_view_height_default', 'int', 400, true),
    'map_provider_available_google_v3' => Input::Get('f_map_provider_available_google_v3', 'int', 0, true),
    'map_provider_available_map_quest' => Input::Get('f_map_provider_available_map_quest', 'int', 0, true),
    'map_provider_available_oSM' => Input::Get('f_map_provider_available_oSM', 'int', 0, true),
    'map_provider_default' => Input::Get('f_map_provider_default', 'string'),
    'map_marker_directory' => Input::Get('f_map_marker_directory', 'string'),
    'map_marker_source_default' => Input::Get('f_map_marker_source_default', 'string'),
    'map_popup_width_min' => Input::Get('f_map_popup_width_min', 'int'),
    'map_popup_height_min' => Input::Get('f_map_popup_height_min', 'int'),
    'map_video_width_you_tube' => Input::Get('f_map_video_width_you_tube', 'int'),
    'map_video_height_you_tube' => Input::Get('f_map_video_height_you_tube', 'int'),
    'map_video_width_vimeo' => Input::Get('f_map_video_width_vimeo', 'int'),
    'map_video_height_vimeo' => Input::Get('f_map_video_height_vimeo', 'int'),
    'map_video_width_flash' => Input::Get('f_map_video_width_flash', 'int'),
    'map_video_height_flash' => Input::Get('f_map_video_height_flash', 'int'),
    'flash_server' => Input::Get('f_flash_server', 'string'),
    'flash_directory' => Input::Get('f_flash_directory', 'string'),
);

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
    exit;
}

$msg_ok = 1;

// Site On-line
SystemPref::Set('SiteOnline', $f_campsite_online);

// Allow Password Recovery
SystemPref::Set('PasswordRecovery', $f_password_recovery);
SystemPref::Set('PasswordRecoveryFrom', $f_password_recovery_from);

// Site title
SystemPref::Set('SiteTitle', $f_site_title);

// Site Meta Keywords
SystemPref::Set('SiteMetaKeywords', $f_site_metakeywords);

// Site Meta Description
SystemPref::Set('SiteMetaDescription', $f_site_metadescription);

// Site Time Zone
SystemPref::Set('TimeZone', $f_time_zone);

// DB Caching
if (SystemPref::Get('DBCacheEngine') != $f_cache_engine) {
    if (!$f_cache_engine || CampCache::IsSupported($f_cache_engine)) {
        SystemPref::Set('DBCacheEngine', $f_cache_engine);
        CampCache::singleton()->clear('user');
        CampCache::singleton()->clear();
    } else {
        $msg_ok = 0;
        camp_html_add_msg(getGS('Invalid: You need PHP $1 enabled in order to use the caching system.', $f_cache_engine));
    }
}

// Template Caching
if (SystemPref::Get('TemplateCacheHandler') != $f_template_cache_handler && $f_template_cache_handler) {
    $handler = CampTemplateCache::factory($f_template_cache_handler);
    if ($handler && CampTemplateCache::factory($f_template_cache_handler)->isSupported()) {
        SystemPref::Set('TemplateCacheHandler', $f_template_cache_handler);
        CampTemplateCache::factory($f_template_cache_handler)->clean();
    } else {
        $msg_ok = 0;
        camp_html_add_msg(getGS('Invalid: You need PHP $1 enabled in order to use the template caching system.'
            , $f_template_cache_handler));
    }
} else {
    SystemPref::Set('TemplateCacheHandler', $f_template_cache_handler);
}

// Image cache lifetime
SystemPref::Set('ImagecacheLifetime', $f_imagecache_lifetime);

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
        $max_upload_filesize_bytes <= min(camp_convert_bytes(ini_get('post_max_size')), camp_convert_bytes(ini_get('upload_max_filesize')))) {
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

// Statistics collecting
SystemPref::Set('CollectStatistics', $f_collect_statistics);

// Image resizing for WYSIWYG editor
if ($f_editor_image_ratio < 1 || $f_editor_image_ratio > 100) {
    $f_editor_image_ratio = 100;
}
SystemPref::Set('EditorImageRatio', $f_editor_image_ratio);
SystemPref::Set('EditorImageResizeWidth', $f_editor_image_width);
SystemPref::Set('EditorImageResizeHeight', $f_editor_image_height);
SystemPref::Set('EditorImageZoom', $f_editor_image_zoom);

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

// template filter
SystemPref::Set("TemplateFilter", $f_template_filter);

// External cron management
SystemPref::Set('ExternalCronManagement', $f_external_cron_management);

// geolocation
foreach ($f_geo as $key => $value) {
    $name = '';
    foreach (explode('_', $key) as $part) {
        $name .= ucfirst($part);
    }
    SystemPref::Set($name, $value);
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
