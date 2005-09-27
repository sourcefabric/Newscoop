<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/templates/lib_upload.php");
    
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl')) {
	header("Location: /$ADMIN/ad.php?ADReason=".encURL(getGS("You do not have the right to modify templates.")));
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
todef('File', $HTTP_POST_FILES[File][tmp_name]);
todef('File_name', $HTTP_POST_FILES[File][name]);

$debugLevelHigh = false;
$debugLevelLow = false;
$res = doUpload("File", $Charset, $Campsite['HTML_DIR'].'/look/'.decS($Path));

if ($res) {
	$fileName = $GLOBALS["File"."_name"];
	$templates_dir = $Campsite['HTML_DIR'] . '/look';
	Template::UpdateStatus();

	$logtext = getGS('Template $1 uploaded', encHTML(decS($fileName)));
	query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=111, User='".$User->getUserName()."', Text='$logtext'");
	header("Location: /$ADMIN/templates?Path=" . encURL($Path));
}

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title"><?php  putGS("Uploading template"); ?></TD>
		<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR>	<TR><TD>&nbsp;</TD></TR></TR></TABLE></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Path"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($Path)); ?></TD>
</TR></TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
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
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".encHTML(decS($Path)); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>
<?php
camp_html_copyright_notice();
?>

</BODY>
</HTML>