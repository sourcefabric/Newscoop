<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");
    
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
$Name = Input::Get('Name', 'string', '');
$cField = Input::Get('cField', 'string', '');

$filename = $Campsite['HTML_DIR']."/look/".decURL($Path)."/$Name";
$fd = fopen ($filename, "w");
$nField = str_replace("\\r", "\r", $cField);
$nField = str_replace("\\n", "\n", $nField);
$nField = decS($nField);
$res = fwrite ($fd, $nField);
fclose ($fd);

if ($nField == 0 || $res > 0) {
	$logtext = getGS('Template $1 was changed', encHTML(decS($Path))."/".encHTML(decS($Name)));
	query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=113, User='".$User->getUserName()."', Text='$logtext'");
	header("Location: /$ADMIN/templates?Path=".encURL(decS($Path)));

?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title"><?php  putGS("Edit template"); ?></TD>
		<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR></TR></TABLE></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Path"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($Path)); ?></TD>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Template"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($Name)); ?></TD>
</TR></TABLE>
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Edit template"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE> <LI><?php putGS('The template has been saved.'); ?></LI> </BLOCKQUOTE></TD>
	</TR>
<?php } else { ?>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE> <LI><?php  putGS('The template could not be saved'); ?></LI> </BLOCKQUOTE></TD>
	</TR>
<?php } ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates?Path=<?php pencURL(decS($Path)); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>
<?php
camp_html_copyright_notice();
?>

</HTML>
