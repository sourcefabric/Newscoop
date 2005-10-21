<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$cPath = Input::Get('cPath', 'string', '');
if (!Template::IsValidPath($cPath)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$cName = Input::Get('cName', 'string', '');
$created = 0;
$correct = trim($cName) != "";
if ($correct) {
	$cName = strtr($cName,'?~#%*&|"\'\\/<>', '_____________');
	$newTempl = Template::GetFullPath($cPath, $cName);
	//$newTempl = $Campsite['HTML_DIR']."/look/".decURL($cPath)."/".$cName;
	$ok = 0;

	$file_exists = file_exists($newTempl);
	if (!$file_exists) {
		$ok = touch ($newTempl);
	}
	if ($ok) {
		Template::UpdateStatus();
		$logtext = getGS('New template $1 was created',$cPath."/".$cName);
		Log::Message($logtext, $User->getUserName(), 114);
		header("Location: /$ADMIN/templates/edit_template.php?Path=$cPath&Name=$cName");
		exit;
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Creating new template"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Creating new template"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
	<?php
	if (!$correct) {
	?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
	<?php
	} else {
		if ($file_exists) {
			putGS('A file or folder having the name $1 already exists','<b>'.$cName.'</B>');
		}
		else {
			putGS('The template $1 could not be created.','<b>'.$cName.'</B>');
		}
	}
	?>	</BLOCKQUOTE></TD>
	</TR>

<?php if ($ok) { ?>	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Do you want to edit the template ?'); ?></LI></BLOCKQUOTE></TD>
	</TR>
<?php } ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php p(urlencode($cPath)); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>
<?php
camp_html_copyright_notice();
?>

</HTML>
