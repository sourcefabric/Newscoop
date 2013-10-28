<?PHP
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error($translator->trans("You do not have the right to manage publications.", array(), 'pub'));
	exit;
}

$f_publication_id = Input::Get('Pub', 'int');
$f_alias_id = Input::Get('Alias', 'int');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($f_publication_id);
$alias = new Alias($f_alias_id);

$crumbs = array($translator->trans("Publication Aliases", array(), 'pub') => "aliases.php?Pub=$f_publication_id");
camp_html_content_top($translator->trans("Edit alias", array(), 'pub'), array("Pub" => $publicationObj), true, false, $crumbs);

camp_html_display_msgs();
?>
<P>
<FORM name="edit_alias" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_edit_alias.php">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  echo $translator->trans("Edit alias", array(), 'pub'); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="hidden" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
<INPUT TYPE="hidden" NAME="f_alias_id" VALUE="<?php p($f_alias_id); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_name" VALUE="<?php echo htmlspecialchars($alias->getName()); ?>" SIZE="32" MAXLENGTH="255" class="input_text">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  echo $translator->trans('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.edit_alias.f_name.focus();
</script>

<?php camp_html_copyright_notice(); ?>
