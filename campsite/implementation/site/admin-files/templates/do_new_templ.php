<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$f_path = Input::Get('f_path', 'string', '');
$f_name = Input::Get('f_name', 'string', '');

if (!Template::IsValidPath($f_path) || !Template::IsValidPath($f_name, false)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

if (trim($f_name) == "") {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
	camp_html_goto_page("/$ADMIN/templates/new_template.php?Path=".urlencode($f_path));
}

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
	Template::UpdateStatus();
	$logtext = getGS('New template $1 created',$f_path."/".$f_name);
	Log::Message($logtext, $g_user->getUserId(), 114);
	camp_html_add_msg($logtext, "ok");
	camp_html_goto_page("/$ADMIN/templates/edit_template.php?f_path=$f_path&f_name=$f_name");
} else {
	camp_html_add_msg(getGS('A file or folder having the name $1 already exists','<b>'.$f_name.'</B>'));
	camp_html_goto_page("/$ADMIN/templates/new_template.php?Path=".urlencode($f_path));
}

?>