<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('DeleteTempl')) {
	header("Location: /$ADMIN/ad.php?ADReason=".encURL(getGS("You do not have the right to delete templates.")));
	exit;
}

$Path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($Path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$Name = Input::Get('Name', 'string', '');
$What = Input::Get('What', 'int', 0);

$dir = decURL(decS($Path))."/".decURL(decS($Name));
$file = $Campsite['HTML_DIR']."/look/".decURL($Path)."/".$Name;
$templates_dir = $Campsite['HTML_DIR']."/look";
$olderr =  error_reporting(0);
$msg_ok = "The template has been deleted.";

if ($What == '0') {
	$msg_ok = "The folder has been deleted.";
	$msg_fail = "The folder could not be deleted.";
	$res = rmdir($file);
} else {
	$template_path = template_path($Path, $Name);
	if (template_is_used($template_path) == false) {
		$msg_fail = "The template could not be deleted.";
		$res = unlink($file);
		if ($res) {
			$logtext = getGS('Template $1 was deleted', encHTML($dir));
			query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=112, User='".$User->getUserName()."', Text='$logtext'");
			verify_templates($templates_dir, $mt, $dt, $errors);
		}
	} else {
		$msg_fail = "The template $1 is in use and can not be deleted.";
		$res = 0;
	}
}

if ($res)
	header("Location: /$ADMIN/templates?Path=" . encURL($Path));
?>
<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
			<?php if ($What == 1) { putGS("Deleting template"); } else { putGS("Deleting folder"); } ?>
		</TD>
		<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/templates/?Path=<?php  pencURL(decS($Path)); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Templates"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/templates/?Path=<?php  pencURL(decS($Path)); ?>" ><B><?php  putGS("Templates");  ?></B></A></TD></TR></TABLE></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR><TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Path"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($Path)); ?></TD></TR>
</TABLE>
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B><?php if($What == 1) { putGS("Deleting template"); } else { putGS("Deleting folder"); } ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	error_reporting($olderr);
	if ($res == 0) $msg = $msg_fail;
	else $msg = $msg_ok;
	print "\t\t\t<LI>";
	putGS($msg, $template_path);
	print "</li>\n";
?>
			</BLOCKQUOTE>
		</TD>
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
CampsiteInterface::CopyrightNotice();
?>

</BODY>
</HTML>
