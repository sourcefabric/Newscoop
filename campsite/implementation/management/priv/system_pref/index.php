<?php
camp_load_translation_strings("system_pref");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SystemPref.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

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
	<td align="left" width="400px">
		<?php putGS("Keyword separator:"); ?>
	</td>
	<td align="left" valign="top">
		<input type="text" name="f_keyword_separator" value="<?php p(SystemPref::Get("KeywordSeparator")); ?>" maxlength="2" size="4" class="input_text" alt="blank" emsg="<?php putGS("Keyword separator must be at least one character."); ?>">
	</td>
</tr>
<tr>
	<td align="left" width="400px" nowrap>
		<?php putGS("Number of failed login attempts before showing CAPTCHA :"); ?>
	</td>
	<td align="left" valign="top">
		<input type="text" name="f_login_num" value="<?php p(SystemPref::Get("LoginFailedAttemptsNum")); ?>" maxlength="2" size="4" class="input_text" alt="number|0|0
|10" emsg="<?php putGS("Please enter a positive number for the '$1' field.", getGS("Login Attempts")); ?>">
	</td>
</tr>
<tr>
	<td align="left">
		<?php putGS("Maximum upload file size:"); ?>
		<div style="padding-top: 3px; padding-left: 15px;"><?php putGS("Enter a value between 0 and ".ini_get('upload_max_filesize')." (the maximum size is specified in 'php.ini'). Please use 'K' for kilobytes, 'M' for megabytes and 'G' for gigabytes, without white spaces between the value and the corresponding letter, e.g. '3M'."); ?></div>
	</td>
	<td valign="top">
		<input type="text" name="f_max_upload_filesize" value="<?php p(SystemPref::Get("MaxUploadFileSize")); ?>" maxlenght="12" size="14" class="input_text" alt="blank" emsg="<?php putGS("Please enter a positive number for the '$2' field.", getGS("Max Upload File Size")); ?>" />
</tr>
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
