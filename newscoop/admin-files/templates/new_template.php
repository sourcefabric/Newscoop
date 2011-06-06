<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to create templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($Path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

$fullPath = $Campsite['TEMPLATE_DIRECTORY'].$Path;
if (!is_writable($fullPath)) {
	camp_html_add_msg(getGS("Unable to $1 template.", 'create'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $fullPath));
	camp_html_goto_page("/$ADMIN/templates/?Path=".urlencode($Path));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Create new template"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();
?>

<P>
<FORM NAME="template_add" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/templates/do_new_templ.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Create new template"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" SIZE="32" alt="blank" emsg="<?php putGS('You must fill in the $1 field.','\''.getGS('Name').'\''); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="HIDDEN" NAME="f_path" VALUE="<?php  p($Path); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.template_add.f_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
