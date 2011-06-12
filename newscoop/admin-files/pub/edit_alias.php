<?PHP
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$f_publication_id = Input::Get('Pub', 'int');
$f_alias_id = Input::Get('Alias', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($f_publication_id);
$alias = new Alias($f_alias_id);

$crumbs = array(getGS("Publication Aliases") => "aliases.php?Pub=$f_publication_id");
camp_html_content_top(getGS("Edit alias"), array("Pub" => $publicationObj), true, false, $crumbs);

camp_html_display_msgs();
?>
<P>
<FORM name="edit_alias" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_edit_alias.php">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Edit alias"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="hidden" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
<INPUT TYPE="hidden" NAME="f_alias_id" VALUE="<?php p($f_alias_id); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_name" VALUE="<?php echo htmlspecialchars($alias->getName()); ?>" SIZE="32" MAXLENGTH="255" class="input_text">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.edit_alias.f_name.focus();
</script>

<?php camp_html_copyright_notice(); ?>
