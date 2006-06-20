<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl') || !$g_user->hasPermission("DeleteTempl")) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$f_path = Input::Get('f_path', 'string', '');
$f_name = Input::Get('f_name', 'string', '');
$backLink  = "/$ADMIN/templates/";
if (!Template::IsValidPath($f_path)) {
	camp_html_goto_page($backLink);
}
$filename = Template::GetFullPath($f_path, $f_name);

if (!file_exists($filename)) {
	camp_html_display_error(getGS("Invalid template file $1" , $f_path."/$f_name"), $backLink);
	exit;
}

$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$imageExtensions = array("png", "jpg", "jpeg", "jpe", "gif");

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($f_path));
$crumbs[] = array(getGS("Edit template").": $f_name", "");
echo camp_html_breadcrumbs($crumbs);

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();

if (in_array($extension, $imageExtensions)) {
	$urlPath = substr($filename, strlen($Campsite['TEMPLATE_DIRECTORY']));
	?>
	<p>
	<table cellpadding="6" style="border: 1px dashed black; margin-left: 15px;">
	<tr>
		<td style="padding: 10px;">
			<img border="0" src="<?php p($Campsite['TEMPLATE_BASE_URL'].$urlPath); ?>?time=<?php p(time()); ?>">
		</td>
	</tr>
	</table>
	<p>
	<?php
} else {
	$contents = file_get_contents($filename);
	?>
	<P>
	<FORM NAME="template_edit" METHOD="POST" ACTION="do_edit.php"  >
	<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<?php  p($f_path); ?>">
	<INPUT TYPE="HIDDEN" NAME="Name" VALUE="<?php  p($f_name); ?>">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
	<TR>
		<TD COLSPAN="2" align="center">
		<?php  if ($g_user->hasPermission("DeleteTempl")) { ?>
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
		<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($f_path); ?>'">
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
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($f_path); ?>'">
		<?php  } ?>
		</TD>
	</TR>
	</TABLE>
	</FORM>
	<SCRIPT>
	document.template_edit.cField.focus();
	</SCRIPT>
<?php } ?>
<p>
<form method="POST" action="do_replace.php" onsubmit="return <?php camp_html_fvalidate(); ?>;" ENCTYPE="multipart/form-data" >
<input type="hidden" name="f_path" value="<?php p(htmlspecialchars($f_path)); ?>">
<input type="hidden" name="f_old_name" value="<?php p(htmlspecialchars($f_name)); ?>">
<table class="table_input" cellpadding="6">
<tr>
	<td>
		<b><?php putGS("Replace file:"); ?></b> <input type="FILE" name="f_file" class="input_file" alt="file|txt,html,htm,php,xml,asp,tpl,py,java,jpg,jpeg,jpe,png,gif,tif,tiff" emsg="<?php putGS("You must select a file to upload."); ?>">
	</td>
</tr>
<tr>
	<td align="center">
		<INPUT type="submit" name="replace" value="<?php putGS("Replace"); ?>" class="button">
	</td>
</tr>
</table>
</form>
<P>

<?php camp_html_copyright_notice(); ?>
