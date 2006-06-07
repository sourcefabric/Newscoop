<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to create new folders."));
	exit;
}

$cPath = Input::Get('cPath', 'string', '');
if (!Template::IsValidPath($cPath)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$cName = Input::Get('cName', 'string', '');
$correct = trim($cName) != '';
if ($correct) {
	if (trim($cName) == '..' || trim($cName) == '.') {
		camp_html_display_error(getGS("The folder name can't be '..' or '.'"),
								"/$ADMIN/templates/?Path=".urlencode($cPath));
		exit;
	}
	$cName = strtr($cName, '?~#%*&|"\'\\/<>', '_____________');
	$newdir = Template::GetFullPath($cPath, $cName);
	$ok = 0;
	$file_exists = file_exists($newdir);
	if (!$file_exists) {
		$dir = mkdir($newdir, 0755);
		$ok = ($dir === true);
	}
}

if ($ok) {
	header("Location: /$ADMIN/templates?Path=" . urlencode("$cPath/$cName"));
	exit;
}
$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($path));
$crumbs[] = array(getGS("Creating new folder"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Creating new folder"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
	<?php
	if (!$correct) { ?>
		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
		<?php
	} else {
		if ($file_exists) {
			putGS('A file or folder having the name $1 already exists','<b>'.$cName.'</B>');
		}
		else {
			putGS('The folder $1 could not be created','<b>'.$cName.'</B>');
		}
	}
	?></BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($cPath); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
