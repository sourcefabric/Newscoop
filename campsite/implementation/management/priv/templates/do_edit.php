<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($Path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$Name = Input::Get('Name', 'string', '');
$cField = Input::Get('cField', 'string', '');
$nField = str_replace("\\r", "\r", $cField);
$nField = str_replace("\\n", "\n", $nField);

$filename = Template::GetFullPath($Path, $Name);
$result = false;
if (@$handle = fopen($filename, 'w')) {
	$result = fwrite($handle, $nField);
	fclose($handle);
}

if ($result !== false) {
	$logtext = getGS('Template $1 was changed', $Path."/".$Name);
	Log::Message($logtext, $g_user->getUserName(), 113);
	header("Location: /$ADMIN/templates/edit_template.php?Path=".urlencode($Path)
			."&Name=".urlencode($Name)."&res=OK"
			."&resMsg=".urlencode(getGS("The template '$1' was saved successfully.", $Name)));
	exit;
} else {
	$errMsg = getGS("Unable to save the template '$1' to the path '$2'.", $Name, $Path) . " "
			. getGS("Please check if the user '$1' has permission to write in this directory.", $Campsite['APACHE_USER']);
}
$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Edit template"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="table_input">
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE> <LI><?php echo $errMsg; ?></LI> </BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates?Path=<?php p(urlencode($Path)); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
