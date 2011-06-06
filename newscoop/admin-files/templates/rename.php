<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to rename templates."));
	exit;
}

$path = Input::Get('Path', 'string', '');
$Name = Input::Get('Name', 'string', '');
if (!Template::IsValidPath($path)) {
	$path = "";
}
$print_path = ($path != "") ? $path : "/";

$fileFullPath = Template::GetFullPath($path, $Name);
if (!is_writable($fileFullPath)) {
	camp_html_add_msg(getGS("Unable to $1 template.", 'rename'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $fileFullPath));
	camp_html_goto_page("/$ADMIN/templates/?Path=".urlencode($path));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($path));
$crumbs[] = array(getGS("Rename template").": $Name", "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/templates/do_rename.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Rename template"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("To New Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_new_name" SIZE="32" alt="blank" emsg="<?php putGS('You must fill in the $1 field.','\''.getGS('To New Name').'\''); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_path" VALUE="<?php p($path); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_orig_name" VALUE="<?php p($Name); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php putGS('Cancel'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/?Path=<?php p(urlencode($path)); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
