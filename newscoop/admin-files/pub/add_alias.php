<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);

$crumbs = array(getGS("Publication Aliases") => "aliases.php?Pub=$Pub");
camp_html_content_top(getGS("Add new alias"), array("Pub" => $publicationObj), true, false, $crumbs);

camp_html_display_msgs();
?>
<P>
<FORM NAME="add_alias" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_add_alias.php">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new alias"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<?php p($Pub); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="255">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
<!--	<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/aliases.php?Pub=<?php  p($Pub); ?>'">
-->
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.forms.add_alias.cName.focus();
</script>
<?php camp_html_copyright_notice(); ?>
