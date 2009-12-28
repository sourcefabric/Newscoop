<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl') && !$g_user->hasPermission('DeleteTempl')) {
	camp_html_goto_page("/$ADMIN/");
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

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="action_buttons">
<TR>
	<?php
	if (!empty($path)) {
		$new_path = substr($path, 0, strrpos($path, '/'));
		?>
		<TD  style="padding-right: 10px;">
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
			<TR>
				<TD><A HREF="?Path=<?php p(urlencode($new_path)); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></A></TD>
				<TD><A HREF="?Path=<?php p(urlencode($new_path)); ?>" ><B><?php  putGS("Go up"); ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
		<?php
	}

	if ($g_user->hasPermission("ManageTempl")) { ?>
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
				<TD><A HREF="/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php  p(urlencode($path)); ?>" ><B><?php  putGS("Create new template"); ?></B></A></TD>
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
<P>
<?php
camp_html_display_msgs("0.5em", 0);

$listbasedir = $path;
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/templates/list_dir.php");
?>
<P>
<?php camp_html_copyright_notice(); ?>
