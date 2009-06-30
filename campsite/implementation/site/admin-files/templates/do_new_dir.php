<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to create new folders."));
	exit;
}

$cPath = Input::Get('cPath', 'string', '');
if (!Template::IsValidPath($cPath)) {
	camp_html_goto_page("/$ADMIN/templates/");
}
$cName = Input::Get('cName', 'string', '');

$backLink = "/$ADMIN/templates/new_dir.php?Path=".urlencode($cPath);

if (trim($cName) == '') {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
	camp_html_goto_page($backLink);
}

if (trim($cName) == '..' || trim($cName) == '.') {
	camp_html_add_msg(getGS("The folder name can't be '..' or '.'"));
	camp_html_goto_page($backLink);
}

$cName = strtr($cName, '?~#%*&|"\'\\/<>', '_____________');
$newdir = Template::GetFullPath($cPath, $cName);
$file_exists = file_exists($newdir);
if (!$file_exists) {
	$dir = mkdir($newdir, 0755);
	if ($dir === true) {
		camp_html_add_msg(getGS("Directory $1 created.", "&quot;".$cName."&quot;"), "ok");
		camp_html_goto_page("/$ADMIN/templates/?Path=" . urlencode("$cPath/$cName"));
	} else {
		camp_html_add_msg(camp_get_error_message(CAMP_ERROR_MKDIR, $newdir));
	}
} else {
	camp_html_add_msg(getGS('A file or folder having the name $1 already exists','&quot;'.$cName.'&quot;'));
}
camp_html_goto_page($backLink);

?>