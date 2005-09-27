<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl') && !$User->hasPermission('DeleteTempl')) {
	header("Location: /$ADMIN/");
	exit;
}

$path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($path))
	$path = "";
$print_path = $path != "" ? $path : "/";

Template::UpdateStatus();

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
			<?php  putGS("Templates"); ?>
		</TD>
		<TD ALIGN=RIGHT>
			&nbsp;
		</TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
	<TR>
		<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Path"); ?>:</TD>
		<TD VALIGN="TOP" class="current_location_content"><B><?php  pencHTML(decURL($print_path)); ?></B></TD>
	</TR>
</TABLE>
<P>
<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">
	<TR>
<?php
if ($path != "") {
	$new_path = substr($path, 0, strrpos($path, '/'));
?>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="?Path=<?php pencURL($new_path); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="?Path=<?php pencURL($new_path); ?>" ><B><?php  putGS("Go up"); ?></B></A></TD></TR></TABLE></TD>
<?php
}
if ($User->hasPermission("ManageTempl")) {
?>

		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_dir.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_dir.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Create new folder"); ?></B></A></TD></TR></TABLE></TD>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/templates/upload_templ.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/templates/upload_templ.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Upload template"); ?></B></A></TD></TR></TABLE></TD>
		<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php  pencURL($path); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Create new template"); ?></B></A></TD></TR></TABLE></TD>
<?PHP
}
?>
	</TR>
</TABLE>
<?php
$listbasedir = $path;
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/templates/list_dir.php");
?>
<P>
<?php
CampsiteInterface::CopyrightNotice();
?>
</HTML>
