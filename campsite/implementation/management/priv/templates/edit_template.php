<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl') || !$g_user->hasPermission("DeleteTempl")) {
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

$res = Input::Get('res', 'string', 'OK');
$resMsg = Input::Get('resMsg', 'string', '');

?>

<?php if ($resMsg != '') { ?>
<p>
<table border="0" cellpadding="0" cellspacing="0" class="action_buttons">
<tr>
<?php if ($res == 'OK') { ?>
	<td class="info_message">
<?php } else { ?>
	<td class="error_message">
<?php } ?>
		<?php echo $resMsg; ?>
	</td>
</tr>
</table>
</p>
<?php } ?>

<P>
<FORM NAME="template_edit" METHOD="POST" ACTION="do_edit.php"  >
<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<?php  p($Path); ?>">
<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<?php  p($Name); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2" align="center">
	<?php  if ($g_user->hasPermission("DeleteTempl")) { ?>
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<?php  } else { ?>
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">
	<?php  } ?>
	</TD>
</TR>

<TR>
	<TD><TEXTAREA ROWS="25" COLS="90" NAME="cField" WRAP="NO"><?php  p(htmlspecialchars($contents)); ?></TEXTAREA></TD>
</TR>

<TR>
	<TD COLSPAN="2" align="center">
	<?php  if ($g_user->hasPermission("DeleteTempl")) { ?>
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<?php  } else { ?>
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">
	<?php  } ?>
	</TD>
</TR>
</TABLE>
</FORM>
<SCRIPT>
document.template_edit.cField.focus();

</SCRIPT>
<?php camp_html_copyright_notice(); ?>
