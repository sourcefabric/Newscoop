<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
}

$f_path = Input::Get('f_path', 'string', '');
if (!Template::IsValidPath($f_path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}
$f_new_name = Input::Get('f_new_name', 'string', '');
$f_orig_name = Input::Get('f_orig_name', 'string', '');

$correct = trim($f_new_name) != "";
$ok = false;
if ($correct) {
	$f_new_name = strtr($f_new_name,'?~#%*&|"\'\\/<>', '_____________');

	// Set the extension of the duplicate to be the same as the original file.
	$orig_path_info = pathinfo($f_orig_name);
	$origExtension = isset($orig_path_info["extension"]) ? $orig_path_info["extension"] : "";
	$new_path_info = pathinfo($f_new_name);
	$newExtension = isset($new_path_info["extension"]) ? $new_path_info["extension"] : "";
	if ($newExtension != $origExtension) {
		if ($f_new_name[strlen($f_new_name)-1] != ".") {
			$f_new_name .= ".";
		}
		$f_new_name .= $origExtension;
	}

	$newTempl = $Campsite['HTML_DIR']."/look/".urldecode($f_path)."/$f_new_name";
	$exists = file_exists($newTempl);
	if (!$exists) {
		$tpl1_name = urldecode($f_path)."/$f_orig_name";
		$tpl1 = $Campsite['HTML_DIR']."/look/".urldecode($f_path)."/$f_orig_name";
		$fd = fopen($tpl1, "r");
		$contents = fread($fd, filesize ($tpl1));
		fclose($fd);

		$tpl2_name = urldecode($f_path)."/$f_new_name";
		$tpl2FullPath = $Campsite['HTML_DIR']."/look/".urldecode($f_path)."/$f_new_name";
		$fd = fopen($tpl2FullPath, "w");
		$bytes_written = fwrite($fd, $contents);
		fclose($fd);
		$ok = ( ($bytes_written !== false) || (strlen($contents) == 0) );
		if ($ok) {
			$logtext = getGS('Template $1 was duplicated into $2', $tpl1_name, $tpl2_name);
			Log::Message($logtext, $g_user->getUserId(), 115);
		}
	}
}

if ($ok) {
	if (camp_is_template_file($tpl2FullPath)) {
		// Go into edit mode.
		camp_html_goto_page("/$ADMIN/templates/edit_template.php"
			."?f_path=".urlencode($f_path)."&f_name=".urlencode($f_new_name));
	} else {
		// Go back to file list.
		camp_html_goto_page("/$ADMIN/templates?Path=".urlencode($f_path));
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($f_path));
$crumbs[] = array(getGS("Duplicate template").": $f_orig_name", "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Duplicate template"); ?> </B>
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
		if (!$ok) {
			putGS('The template $1 could not be created.','<b>'.$f_new_name.'</B>');
		}
	}
	?>
	</BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/dup.php?Path=<?php  p(urlencode($f_path)); ?>&Name=<?php  p(urlencode($f_orig_name)); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>