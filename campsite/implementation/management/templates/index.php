<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$path = Input::get('Path', 'string', '');

?>
<HEAD>
	<TITLE><?php  putGS("Templates management"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">	
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/admin/img/sign_big.gif" BORDER="0"></TD>
		<TD>
			<DIV STYLE="font-size: 12pt"><B><?php  putGS("Templates"); ?></B></DIV>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT>
			<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
				<TR>
					<TD><A HREF="/admin/home.php" ><IMG SRC="/admin/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD>
					<TD><A HREF="/admin/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
					<TD><A HREF="/admin/logout.php" ><IMG SRC="/admin/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD>
					<TD><A HREF="/admin/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Path"); ?>:</TD>
		<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  pencHTML(decURL($path)); ?></B></TD>
	</TR>
</TABLE>
<P>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
	<TR>
<?php
if ($path != "") {
	$new_path = substr($path, 0, strrpos($path, '/'));
?>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="?Path=<?php pencURL($new_path); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="?Path=<?php pencURL($new_path); ?>" ><B><?php  putGS("Go up"); ?></B></A></TD></TR></TABLE></TD>
<?php
}

if ($User->hasPermission('ManageTempl')) {
?>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/new_dir.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/new_dir.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Create new folder"); ?></B></A></TD></TR></TABLE></TD>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/upload_templ.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/upload_templ.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Upload template"); ?></B></A></TD></TR></TABLE></TD>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/new_template.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/new_template.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Create new template"); ?></B></A></TD></TR></TABLE></TD>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/admin/templates/refresh.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/admin/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/admin/templates/refresh.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Refresh templates directory"); ?></B></A></TD></TR></TABLE></TD>
  <?php  }
?>
	</TR>
</TABLE>
<P>
<?php

$listbasedir = "/look/$path";
$dta = $User->hasPermission('DeleteTempl');
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/templates/list_dir.php");

CampsiteInterface::CopyrightNotice();
?>
</HTML>
