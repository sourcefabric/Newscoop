<?php
camp_load_translation_strings("system_pref");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SystemPref.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/XR_CcClient.php");


if (!$g_user->hasPermission('ChangeSystemPreferences')) {
	camp_html_display_error(getGS("You do not have the right to change system preferences."));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("System Preferences"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

$max_upload_filesize = SystemPref::Get("MaxUploadFileSize");
if(empty($max_upload_filesize) || $max_upload_filesize == 0) {
	SystemPref::Set("MaxUploadFileSize",ini_get('upload_max_filesize'));
}

?>
<p></p>

<?php camp_html_display_msgs(); ?>

<form action="do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<table border="0" cellspacing="6" align="left" class="table_input" width="500px">
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
		<input type="text" name="f_site_title" value="<?php p(SystemPref::Get("SiteTitle")); ?>" maxlength="100" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the site title") ?>" />
	</td>
</tr>
<tr>
	<td align="left" width="400px">
        <?php putGS("Site Meta Keywords:"); ?>
	</td>
	<td align="left" valign="top">
		<input type="text" name="f_site_metakeywords" value="<?php p(SystemPref::Get("SiteMetaKeywords")); ?>" maxlength="100" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the site meta keywords") ?>" />
	</td>
</tr>
<tr>
	<td align="left" width="400px">
        <?php putGS("Site Meta Description:"); ?>
	</td>
	<td align="left" valign="top">
		<input type="text" name="f_site_metadescription" value="<?php p(SystemPref::Get("SiteMetaDescription")); ?>" maxlength="150" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the site meta description") ?>"/>
	</td>
</tr>
<tr>
    <td align="left" width="400px">
        <?php putGS("Cache Enabled:") ?>
    </td>
    <td align="left" valign="top">
        <input type="radio" name="f_cache_enabled" value="Y" <?php if (SystemPref::Get("SiteCacheEnabled") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_cache_enabled" value="N" <?php if (SystemPref::Get("SiteCacheEnabled") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
    </td>
</tr>
<tr>
	<td align="left" width="400px">
		<?php putGS("Secret Key:"); ?>
	</td>
	<td align="left" valign="top">
		<input type="text" name="f_secret_key" value="<?php p(SystemPref::Get("SiteSecretKey")); ?>" maxlength="32" size="40" class="input_text" alt="blank" emsg="<?php putGS("Please enter the secret key") ?>" />
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
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td colspan="2" align="left">
        <?php putGS("Reader subscriptions managed externally?"); ?>
        <input type="radio" name="f_external_subs_management" value="Y" <?php if (SystemPref::Get("ExternalSubscriptionManagement") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_external_subs_management" value="N" <?php if (SystemPref::Get("ExternalSubscriptionManagement") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
	</td>
</tr>
<tr>
	<td colspan="2"><hr></td>
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
	<td colspan="2"><hr></td>
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
<?php CampPlugin::PluginAdminHooks(__FILE__); ?> 
<tr>
	<td colspan="2" align="center" style="padding-top: 10px;">
		<input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
	</td>
</tr>
</table>
</form>
<p></p>
<br>
<br>
<?php //camp_html_copyright_notice(); ?>
