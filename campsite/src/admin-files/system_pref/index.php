<?php
camp_load_translation_strings("system_pref");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");
require_once(dirname(dirname(dirname(__FILE__))).'/classes/cache/CacheEngine.php');


if (!$g_user->hasPermission('ChangeSystemPreferences')) {
    camp_html_display_error(getGS("You do not have the right to change system preferences."));
    exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("System Preferences"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

$htmlFormData['SiteTitle'] = htmlspecialchars(SystemPref::Get("SiteTitle"));
$htmlFormData['SiteMetaKeywords'] = htmlspecialchars(SystemPref::Get("SiteMetaKeywords"));
$htmlFormData['SiteMetaDescription'] = htmlspecialchars(SystemPref::Get("SiteMetaDescription"));
$htmlFormData['SiteSecretKey'] = htmlspecialchars(SystemPref::Get("SiteSecretKey"));

$max_upload_filesize = SystemPref::Get("MaxUploadFileSize");
if(empty($max_upload_filesize) || $max_upload_filesize == 0) {
    SystemPref::Set("MaxUploadFileSize",ini_get('upload_max_filesize'));
}

$availableCacheEngines = CacheEngine::AvailableEngines();
$availableTemplateCacheHandlers = CampTemplateCache::availableHandlers();
?>
<p></p>

<?php camp_html_display_msgs(); ?>

<form action="do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
    <td colspan="2" align="left">
        <strong><?php putGS("General Settings"); ?></strong>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Site On-Line:") ?>
    </td>
    <td align="left" valign="top">
        <input type="radio" name="f_campsite_online" value="Y" <?php if (SystemPref::Get("SiteOnline") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_campsite_online" value="N" <?php if (SystemPref::Get("SiteOnline") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Site Title:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_site_title" value="<?php p($htmlFormData['SiteTitle']); ?>" maxlength="100" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the site title") ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Site Meta Keywords:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_site_metakeywords" value="<?php p($htmlFormData['SiteMetaKeywords']); ?>" maxlength="100" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the site meta keywords") ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Site Meta Description:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_site_metadescription" value="<?php p($htmlFormData['SiteMetaDescription']); ?>" maxlength="150" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the site meta description") ?>"/>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Time Zone:") ?>
    </td>
    <td align="left" valign="top">
        <select name="f_time_zone" class="input_select">
        <?php
        $timeZone = SystemPref::Get('TimeZone');
        camp_html_select_option('', $timeZone, getGS('disabled'));
        for ($k = -12; $k < 13; $k++) {
            $v = $k < 0 ? $k : '+' . $k;
            camp_html_select_option($v, $timeZone, "GMT $v:00");
        }
        ?>
        </select>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Database Cache Engine:") ?>
    </td>
    <td align="left" valign="top">
        <select name="f_cache_engine" class="input_select">
        <?php
        $DBcacheEngine = SystemPref::Get('DBCacheEngine');
        camp_html_select_option('', $DBcacheEngine, getGS('disabled'));
        foreach ($availableCacheEngines as $cacheEngineName=>$engineData) {
            camp_html_select_option($cacheEngineName, $DBcacheEngine, $cacheEngineName);
        }
        ?>
        </select>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Template Cache Handler:") ?>
    </td>
    <td align="left" valign="top">
        <select name="f_template_cache_handler" class="input_select">
        <?php
        $templateCacheHandler = SystemPref::Get('TemplateCacheHandler');
        camp_html_select_option('', $TemplateCacheHandler, getGS('disabled'));
        foreach ($availableTemplateCacheHandlers as $handler => $value) {
            camp_html_select_option($handler, $templateCacheHandler, $handler);
        }
        ?>
        </select>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Imagecache Lifetime:") ?>
    </td>
    <td align="left" valign="top">
        <select name="f_imagecache_lifetime" class="input_select">
        <?php
        $ttl = SystemPref::Get('ImagecacheLifetime');
        foreach (array(0 => 'disabled',
                       30   => '30 Seconds',
                       60   => '1 Minute',
                       300  => '5 Minutes',
                       900  => '15 Minutes',
                       1800 => '30 Minutes',
                       3600 => '1 Hour',
                       3600*24 => '1 Day',
                       3600*24*2 => '2 Days',
                       3600*24*3 => '3 Days',
                       3600*24*4 => '4 Days',
                       3600*24*5 => '5 Days',
                       3600*24*6 => '6 Days',
                       3600*24*7 => '1 Week',
                       3600*24*14 => '2 Weeks',
                       3600*24*21 => '3 Weeks',
                       3600*24*31 => '1 Month',
                       3600*24*61 => '2 Months',
                       3600*24*91 => '3 Months',
                       3600*24*183 => '6 Months',
                       3600*24*365 => '1 Year',
                       -1          => 'Infinite') as $k => $v) {
            camp_html_select_option($k, $ttl, $v);
        }
        ?>
        </select>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Allow password recovery:") ?>
    </td>
    <td align="left" valign="top">
        <input type="radio" name="f_password_recovery" value="Y" <?php if (SystemPref::Get("PasswordRecovery") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_password_recovery" value="N" <?php if (SystemPref::Get("PasswordRecovery") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Secret Key:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_secret_key" value="<?php p($htmlFormData['SiteSecretKey']); ?>" maxlength="32" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the secret key") ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Session Lifetime:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_session_lifetime" value="<?php p(SystemPref::Get("SiteSessionLifeTime")); ?>" maxlength="4" size="5" class="input_text" alt="number|0|0" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("Session Lifetime")); ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Keyword separator:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_keyword_separator" value="<?php p(SystemPref::Get("KeywordSeparator")); ?>" maxlength="2" size="4" class="input_text" alt="blank" emsg="<?php putGS("Keyword separator must be at least one character."); ?>" emsg="<?php putGS("Please enter the Keyword Seperator") ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px" nowrap>
        <?php putGS("Number of failed login attempts before showing CAPTCHA :"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_login_num" value="<?php p(SystemPref::Get("LoginFailedAttemptsNum")); ?>" maxlength="2" size="4" class="input_text" alt="number|0|0|10" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("Login Attempts")); ?>" />
    </td>
</tr>
<tr>
    <td align="left">
        <?php putGS("Maximum upload file size:"); ?>
        <div style="padding-top: 3px; padding-left: 15px;"><?php putGS("Enter a value between 0 and $1 (the maximum size is specified in 'php.ini'). Please use 'K' for kilobytes, 'M' for megabytes and 'G' for gigabytes, without white spaces between the value and the corresponding letter, e.g. '3M'.", ini_get('upload_max_filesize')); ?></div>
    </td>
    <td valign="top">
        <input type="text" name="f_max_upload_filesize" value="<?php p(SystemPref::Get("MaxUploadFileSize")); ?>" maxlenght="12" size="14" class="input_text" alt="blank" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("Max Upload File Size")); ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("SMTP Host:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_smtp_host" value="<?php p(SystemPref::Get("SMTPHost")); ?>" maxlength="100" size="40" class="input_text" alt="blank" emsg="<?php putGS("SMTP Host must be at least four character."); ?>" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("SMTP Port:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_smtp_port" value="<?php p(SystemPref::Get("SMTPPort")); ?>" maxlength="6" size="8" class="input_text" alt="number|0|0" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("SMTP Port")); ?>" />
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2" align="left">
        <strong><?php putGS("Editor Settings"); ?></strong>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Image Resizing Ratio:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_editor_image_ratio" value="<?php p(SystemPref::Get("EditorImageRatio")); ?>" maxlength="3" size="8" class="input_text" alt="number|0|1|100|bok" emsg="<?php putGS("Please enter a valid value (1 - 100) for the '$1' field.", getGS("Image Resizing Ratio")); ?>" />&nbsp;&#37;
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Image Resizing Width:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_editor_image_width" value="<?php p(SystemPref::Get("EditorImageResizeWidth")); ?>" maxlength="6" size="8" class="input_text" alt="number|0|0|bok" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("Image Resizing Width")); ?>" />&nbsp;pixels
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Image Resizing Height:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_editor_image_height" value="<?php p(SystemPref::Get("EditorImageResizeHeight")); ?>" maxlength="6" size="8" class="input_text" alt="number|0|0|bok" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("Image Resizing Height")); ?>" />&nbsp;pixels
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Zoom enabled for images in article content?"); ?>
    </td>
    <td align="left" valign="top">
        <input type="radio" name="f_editor_image_zoom" value="Y" <?php if (SystemPref::Get("EditorImageZoom") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_editor_image_zoom" value="N" <?php if (SystemPref::Get("EditorImageZoom") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2" align="left">
        <?php putGS("Reader subscriptions managed externally?"); ?>
        <input type="radio" name="f_external_subs_management" value="Y" <?php if (SystemPref::Get("ExternalSubscriptionManagement") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_external_subs_management" value="N" <?php if (SystemPref::Get("ExternalSubscriptionManagement") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2" align="left">
        <?php putGS("Setting up a Campsite Replication Server?"); ?>
        <input type="radio" name="f_use_replication" onclick="ShowElement('replication_server');" value="Y" <?php if (SystemPref::Get("UseDBReplication") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_use_replication" onclick="HideElement('replication_server');" value="N" <?php if (SystemPref::Get("UseDBReplication") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr id="replication_server" style="display: <?php (SystemPref::Get("UseDBReplication") == 'Y') ? p("") : p("none") ?>;">
    <td colspan="2">
        <table cellpadding="0" cellspacing="6">
        <tr>
            <td colspan="2" align="left">
                <strong><?php putGS("Campsite Online Server Database"); ?></strong>
            </td>
        </tr>
        <tr>
            <td width="400px" nowrap>
                <?php putGS("Database Replication Host:") ?>
            </td>
            <td>
                <input type="text" name="f_db_repl_host" value="<?php p(SystemPref::Get("DBReplicationHost")); ?>" maxlenght="60" size="30" class="input_text" />
            </td>
        </tr>
        <tr>
            <td nowrap>
                <?php putGS("Database Replication User:") ?>
            </td>
            <td>
                <input type="text" name="f_db_repl_user" value="<?php p(SystemPref::Get("DBReplicationUser")); ?>" maxlenght="20" size="22" class="input_text" />
            </td>
        </tr>
        <tr>
            <td nowrap>
                <?php putGS("Database Replication Password:") ?>
            </td>
            <td>
                <input type="text" name="f_db_repl_pass" value="<?php p(SystemPref::Get("DBReplicationPass")); ?>" maxlenght="20" size="22" class="input_text" />
            </td>
        </tr>
        <tr>
            <td nowrap>
                <?php putGS("Database Replication Port:") ?>
            </td>
            <td>
                <input type="text" name="f_db_repl_port" value="<?php p(SystemPref::Get("DBReplicationPort")); ?>" maxlenght="6" size="8" class="input_text" />
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2" align="left">
        <?php putGS("Enable Campcaster audioclip attachments?"); ?>
        <input type="radio" name="f_use_campcaster" onclick="ShowElement('campcaster_server');" value="Y" <?php if (SystemPref::Get("UseCampcasterAudioclips") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_use_campcaster" onclick="HideElement('campcaster_server');" value="N" <?php if (SystemPref::Get("UseCampcasterAudioclips") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr id="campcaster_server" style="display: <?php (SystemPref::Get("UseCampcasterAudioclips") == 'Y') ? p("") : p("none") ?>;">
    <td colspan="2" align="left">
        <table cellpadding="0" cellspacing="6">
        <tr>
            <td>
                <strong><?php putGS("Campcaster Server"); ?></strong>
            </td>
        </tr>
        <tr>
            <td width="400px">
                <?php putGS("Hostname / IP Address:"); ?>
            </td>
            <td>
                <input type="text" name="f_cc_hostname" value="<?php p(SystemPref::Get("CampcasterHostName")); ?>" maxlenght="60" size="30" class="input_text" />
            </td>
        </tr>
        <tr>
            <td>
                <?php putGS("Port:"); ?>
            </td>
            <td>
                <input type="text" name="f_cc_hostport" value="<?php p(SystemPref::Get("CampcasterHostPort")); ?>" maxlenght="60" size="30" class="input_text" />
            </td>
        </tr>
        <tr>
            <td>
                <?php putGS("XML RPC Path:"); ?>
            </td>
            <td>
                <input type="text" name="f_cc_xrpcpath" value="<?php p(SystemPref::Get("CampcasterXRPCPath")); ?>" maxlenght="100" size="30" class="input_text" />
            </td>
        </tr>
        <tr>
            <td>
                <?php putGS("XML RPC File:"); ?>
            </td>
            <td>
                <input type="text" name="f_cc_xrpcfile" value="<?php p(SystemPref::Get("CampcasterXRPCFile")); ?>" maxlenght="50" size="30" class="input_text" />
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left">
        <?php putGS("Templates filter mask (separated by comma)"); ?>
    </td>
    <td>
        <input type="text" name="f_template_filter" value="<?php p(SystemPref::Get("TemplateFilter")) ?>" maxlenght="50" size="30" class="input_text"/>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2" align="left">
        <?php putGS("Run cron tasks externaly?"); ?>
        <input type="radio" name="f_external_cron_management" value="Y" <?php if (SystemPref::Get("ExternalCronManagement") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_external_cron_management" value="N" <?php if (SystemPref::Get("ExternalCronManagement") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2" align="left">
        <strong><?php putGS("Geolocation Settings"); ?></strong>
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Center Latitude:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_center_latitude_default" value="<?php p(SystemPref::Get('MapCenterLatitudeDefault')); ?>" maxlength="10" size="10" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Center Longitude:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_center_longitude_default" value="<?php p(SystemPref::Get('MapCenterLongitudeDefault')); ?>" maxlength="10" size="10" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Zoom Level:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_display_resolution_default" value="<?php p(SystemPref::Get('MapDisplayResolutionDefault')); ?>" maxlength="2" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Default Width:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_default_width" value="<?php p(SystemPref::Get('MapDefaultWidth')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Default Height:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_default_height" value="<?php p(SystemPref::Get('MapDefaultHeight')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Allow Google Map Provider:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="checkbox" name="f_map_provider_available_google_v3" value="1" <?php echo SystemPref::Get('MapProviderAvailableGoogleV3') > 0 ? 'checked="checked"' : '';  ?> class="input_checkbox" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Allow OpenStreet Map Provider:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="checkbox" name="f_map_provider_available_oSM" value="1" <?php echo SystemPref::Get('MapProviderAvailableOSM') > 0 ? 'checked="checked"' : '';  ?> class="input_checkbox" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Default Map Provider:") ?>
    </td>
    <td align="left" valign="top">
        <select name="f_map_provider_default" class="input_select">
            <?php
            camp_html_select_option('GoogleV3', SystemPref::Get('MapProviderDefault'), 'Google Map');
            camp_html_select_option('OSM', SystemPref::Get('MapProviderDefault'), 'OpenStreet Map');
            ?>
        </select>
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Marker Directory:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_marker_directory" value="<?php p(SystemPref::Get('MapMarkerDirectory')); ?>" maxlength="80" size="40" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Marker Names:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_marker_names" value="<?php p(SystemPref::Get('MapMarkerNames')); ?>" maxlength="80" size="40" class="input_text" />
    </td>
</tr>
<?php foreach (explode(',', SystemPref::Get('MapMarkerNames')) as $name) { ?>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php echo ucfirst($name), ' ', getGS('Marker Source'); ?>
    </td>
    <td align="left" valign="top">
    <input type="text" name="f_map_marker_source_<?php echo $name; ?>" value="<?php echo SystemPref::Get('MapMarkerSource' . ucfirst($name)); ?>" maxlength="80" size="40" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php echo ucfirst($name), ' ', getGS('Marker Offset X'); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_marker_offsetX_<?php echo $name; ?>" value="<?php echo SystemPref::Get('MapMarkerOffsetX' . ucfirst($name)); ?>" maxlength="4" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php echo ucfirst($name), ' ', getGS('Marker Offset Y'); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_marker_offsetY_<?php echo $name; ?>" value="<?php echo SystemPref::Get('MapMarkerOffsetY' . ucfirst($name)); ?>" maxlength="4" size="4" class="input_text" />
    </td>
</tr>
<?php } // foreach ?>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Popup Minimal Width:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_popup_width_min" value="<?php p(SystemPref::Get('MapPopupWidthMin')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Map Popup Minimal Height:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_popup_height_min" value="<?php p(SystemPref::Get('MapPopupHeightMin')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Youtube Default Width:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_video_width_you_tube" value="<?php p(SystemPref::Get('MapVideoWidthYouTube')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Youtube Default Height:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_video_height_you_tube" value="<?php p(SystemPref::Get('MapVideoHeightYouTube')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Vimeo Default Width:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_video_width_vimeo" value="<?php p(SystemPref::Get('MapVideoWidthVimeo')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Vimeo Default Height:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_video_height_vimeo" value="<?php p(SystemPref::Get('MapVideoHeightVimeo')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Flash Default Width:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_video_width_flash" value="<?php p(SystemPref::Get('MapVideoWidthFlash')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Flash Default Height:"); ?>
    </td>
    <td align="left" valign="top">
        <input type="text" name="f_map_video_height_flash" value="<?php p(SystemPref::Get('MapVideoHeightFlash')); ?>" maxlength="3" size="4" class="input_text" />
    </td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<?php CampPlugin::PluginAdminHooks(__FILE__); ?>
<tr>
    <td colspan="2" align="center" style="padding-top: 10px;">
        <input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
    </td>
</tr>
</table>
</form>
<p></p>
<br />
<br />
<?php //camp_html_copyright_notice(); ?>
