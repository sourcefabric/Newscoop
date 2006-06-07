<?php
camp_load_translation_strings("system_pref");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SystemPref.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

if (!$g_user->hasPermission('ChangeSystemPreferences')) {
	camp_html_display_error(getGS("You do not have the right to change system preferences."));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("System Preferences"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<p></p>
<form action="do_edit.php">
<table border="0" cellspacing="6" align="left" class="table_input">
<tr>
	<td align="left">
		<?php putGS("Keyword separator:"); ?>
	</td>
	<td>
		<input type="text" name="f_keyword_separator" value="<?php p(SystemPref::Get("KeywordSeparator")); ?>" maxlength="2" size="4" class="input_text">
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
	</td>
</tr>
</table>
</form>
<p></p>
<p></p>
<form action="do_edit.php">
<table border="0" cellspacing="6" align="left" class="table_input">
<tr>
	<td align="left">
		<?php putGS("Number of failed Login Attempts before showing CAPTCHA :"); ?>
	</td>
	<td>
		<input type="text" name="f_login_num" value="<?php p(SystemPref::Get("FailedAttemptsNum")); ?>" maxlength="2" size="4" class="input_text">
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="submit" name="save" value="<?php putGS("Save"); ?>" class="button">
	</td>
</tr>
</table>
</form>
<p></p>
<br>
<br>
<?php //camp_html_copyright_notice(); ?>
