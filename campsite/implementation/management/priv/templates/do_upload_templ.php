<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/templates/lib_upload.php");
    
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($Path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$Charset = Input::Get('Charset', 'string', '');
$UNIQUE_ID = Input::Get('UNIQUE_ID', 'string', '');
$Id = Input::Get('Id', 'int', 0);
$File = isset($HTTP_POST_FILES['File']['tmp_name']) ? $HTTP_POST_FILES['File']['tmp_name'] : '';
$File_name = isset($HTTP_POST_FILES['File']['name']) ? $HTTP_POST_FILES['File']['name'] : '';

$debugLevelHigh = false;
$debugLevelLow = false;
$res = Template::OnUpload("File", $Charset, $Path);

if ($res) {
	$fileName = $GLOBALS["File"."_name"];
	Template::UpdateStatus();

	$logtext = getGS('Template $1 uploaded', $fileName);
	Log::Message($logtext, $User->getUserName, 111);
	header("Location: /$ADMIN/templates?Path=" . urlencode($Path));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
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
	<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  p($FSresult)?> </LI> </BLOCKQUOTE></TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
