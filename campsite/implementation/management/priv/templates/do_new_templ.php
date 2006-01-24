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

$f_path = Input::Get('f_path', 'string', '');
if (!Template::IsValidPath($f_path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$f_name = Input::Get('f_name', 'string', '');
$created = 0;
$correct = trim($f_name) != "";
if ($correct) {
	$f_name = strtr($f_name,'?~#%*&|"\'\\/<>', '_____________');
	
	// Set the extension of the new file if it doesnt have one already.
	$new_path_info = pathinfo($f_name);
	$newExtension = isset($new_path_info["extension"]) ? $new_path_info["extension"] : "";
	if (empty($newExtension)) {
		if ($f_name[strlen($f_name)-1] != ".") {
			$f_name .= ".";
		} 
		$f_name .= "tpl";			
	}

	$newTempl = Template::GetFullPath($f_path, $f_name);
	$ok = 0;

	$file_exists = file_exists($newTempl);
	if (!$file_exists) {
		$ok = touch ($newTempl);
	}
	if ($ok) {
		Template::UpdateStatus();
		$logtext = getGS('New template $1 was created',$f_path."/".$f_name);
		Log::Message($logtext, $User->getUserName(), 114);
		header("Location: /$ADMIN/templates/edit_template.php?Path=$f_path&Name=$f_name");
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
			putGS('A file or folder having the name $1 already exists','<b>'.$f_name.'</B>');
		}
		else {
			putGS('The template $1 could not be created.','<b>'.$f_name.'</B>');
		}
	}
	?>	</BLOCKQUOTE></TD>
	</TR>

	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/new_template.php?Path=<?php p(urlencode($f_path)); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>
<?php
camp_html_copyright_notice();
?>

</HTML>
