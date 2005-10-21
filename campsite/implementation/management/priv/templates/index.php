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
if (!Template::IsValidPath($path)) {
	$path = "";
}

Template::UpdateStatus();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($path));
echo camp_html_breadcrumbs($crumbs);

?>
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="action_buttons">
<TR>
	<?php
	if (!empty($path)) {
		$new_path = substr($path, 0, strrpos($path, '/'));
		?>
		<!--<TD  style="padding-right: 10px;">
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
			<TR>
				<TD><A HREF="?Path=<?php p(urlencode($new_path)); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></A></TD>
				<TD><A HREF="?Path=<?php p(urlencode($new_path)); ?>" ><B><?php  putGS("Go up"); ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>-->
		<?php
	}

	if ($User->hasPermission("ManageTempl")) { ?>
		<TD>
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_dir.php?Path=<?php p(urlencode($path)); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_dir.php?Path=<?php p(urlencode($path)); ?>"><B><?php  putGS("Create new folder"); ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
		<TD style="padding-left: 10px;">
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php p(urlencode($path)); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php  pencURL($path); ?>" ><B><?php  putGS("Create new template"); ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
		<TD style="padding-left: 10px;">
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/upload_templ.php?Path=<?php p(urlencode($path)); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></A></TD>
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/upload_templ.php?Path=<?php p(urlencode($path)); ?>" ><B><?php  putGS("Upload template"); ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
	<?PHP
	}
	?>
	</TR>
</TABLE>
<p>
<?php
$listbasedir = $path;
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/templates/list_dir.php");
?>
<P>
<?php
camp_html_copyright_notice();
?>
</HTML>
