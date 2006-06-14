<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$f_path = Input::Get('f_path', 'string', '');
$f_charset = Input::Get('f_charset', 'string', '');
$fileName = isset($_FILES['f_file']['name']) ? $_FILES['f_file']['name'] : '';

if (!Template::IsValidPath($f_path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

$success = Template::OnUpload("f_file", $f_path, null, $f_charset);

if ($success) {
	Template::UpdateStatus();
	camp_html_add_msg(getGS('File "$1" uploaded.', $fileName), "ok");
	camp_html_goto_page("/$ADMIN/templates?Path=" . urlencode($f_path));
} else {
	$errMsg = getGS("Unable to save the template '$1' to the path '$2'.", $fileName, $f_path) . " "
			. getGS("Please check if the user '$1' has permission to write in this directory.", $Campsite['APACHE_USER']);
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($f_path));
$crumbs[] = array(getGS("Uploading template"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Uploading template"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  p($errMsg)?> </LI> </BLOCKQUOTE></TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($f_path); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
