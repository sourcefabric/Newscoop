<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl') || !$User->hasPermission("DeleteTempl")) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
$Name = Input::Get('Name', 'string', '');
if (!Template::IsValidPath($Path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$filename = Template::GetFullPath($Path, $Name);

if (!file_exists($filename)) {
	camp_html_display_error(getGS("Invalid template file $1" , $Path."/$Name"));
	exit;
}

$contents = file_get_contents($filename);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Edit template").": $Name", "");
echo camp_html_breadcrumbs($crumbs);
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php"  >
<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<?php  p($Path); ?>">
<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<?php  p($Name); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Edit template"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<?php  if ($User->hasPermission("DeleteTempl")) { ?>
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">-->
	<?php  } else { ?>
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">
	<?php  } ?>
	</DIV>
	</TD>
</TR>

<TR>
	<TD><TEXTAREA ROWS="25" COLS="85" NAME="cField" WRAP="NO"><?php  p($contents) ?></TEXTAREA></TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<?php  if ($User->hasPermission("DeleteTempl")) { ?>
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">-->
	<?php  } else { ?>
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">
	<?php  } ?>
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>

<?php camp_html_copyright_notice(); ?>
