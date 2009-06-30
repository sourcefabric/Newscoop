<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
}

$f_path = Input::Get('f_path', 'string', '');
if (!Template::IsValidPath($f_path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}
$f_new_name = Input::Get('f_new_name', 'string', '');
$f_orig_name = Input::Get('f_orig_name', 'string', '');

$backLink = "/$ADMIN/templates/dup.php?Path=".urlencode($f_path)."&Name=".urlencode($f_orig_name);
if (trim($f_new_name) == "") {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
	camp_html_goto_page($backLink);
}
$ok = false;
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

$newTempl = $Campsite['HTML_DIR']."/templates/".urldecode($f_path)."/$f_new_name";
$exists = file_exists($newTempl);
if (!$exists) {
	$tpl1_name = urldecode($f_path)."/$f_orig_name";
	$tpl1 = $Campsite['HTML_DIR']."/templates/".urldecode($f_path)."/$f_orig_name";
	$fd = fopen($tpl1, "r");
	$fileSize = filesize($tpl1);
	$contents = $fileSize > 0 ? fread($fd, $fileSize) : '';
	fclose($fd);

	$tpl2_name = urldecode($f_path)."/$f_new_name";
	$tpl2FullPath = $Campsite['HTML_DIR']."/templates/".urldecode($f_path)."/$f_new_name";
	$fd = fopen($tpl2FullPath, "w");
	$bytes_written = fwrite($fd, $contents);
	fclose($fd);
	$ok = ( ($bytes_written !== false) || (strlen($contents) == 0) );
	if ($ok) {
		$logtext = getGS('Template $1 was duplicated into $2', $tpl1_name, $tpl2_name);
		Log::Message($logtext, $g_user->getUserId(), 115);
		if (camp_is_text_file($tpl2FullPath) || camp_is_image_file($tpl2FullPath)) {
			// Go into edit mode.
			camp_html_goto_page("/$ADMIN/templates/edit_template.php"
				."?f_path=".urlencode($f_path)."&f_name=".urlencode($f_new_name));
		} else {
			// Go back to file list.
			camp_html_goto_page("/$ADMIN/templates/?Path=".urlencode($f_path));
		}
	}
} else {
	camp_html_add_msg(getGS('A file or folder having the name $1 already exists','<b>'.$f_new_name.'</B>'));
	camp_html_goto_page("/$ADMIN/templates/new_template.php?Path=".urlencode($f_path));
}

camp_html_add_msg(getGS('The template $1 could not be created.','<b>'.$f_new_name.'</B>'));
camp_html_goto_page($backLink);

?>