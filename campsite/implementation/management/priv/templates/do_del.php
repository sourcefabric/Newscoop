<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('DeleteTempl')) {
	camp_html_display_error(getGS("You do not have the right to delete templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
$Name = Input::Get('Name', 'string', '');
$isFile = Input::Get('What', 'int', 0);

if (!Template::IsValidPath($Path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

$backLink = "/$ADMIN/templates?Path=".urlencode($Path);
$dir = urldecode($Path)."/".urldecode($Name);
$fileFullPath = Template::GetFullPath(urldecode($Path), $Name);
$errorMsgs = array();


$deleted = false;
if (!$isFile) {
	$deleted = rmdir($fileFullPath);
	if ($deleted) {
		$logtext = getGS('Directory $1 was deleted', mysql_real_escape_string($dir));
		Log::Message($logtext, $g_user->getUserName(), 112);
		camp_html_add_msg($logtext, "ok");
	} else {
		camp_html_add_msg(camp_get_error_message(CAMP_ERROR_RMDIR, $fileFullPath));
	}
} else {
	$template_path = Template::GetPath($Path, $Name);
	if (!Template::InUse($Name)) {
		$deleted = unlink($fileFullPath);
		if ($deleted) {
			$logtext = getGS('Template $1 was deleted', mysql_real_escape_string($dir));
			Log::Message($logtext, $g_user->getUserName(), 112);
			Template::UpdateStatus();
			camp_html_add_msg($logtext, "ok");
		} else {
			camp_html_add_msg(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $fileFullPath));
		}
	} else {
		camp_html_add_msg(getGS("The template $1 is in use and can not be deleted.", $fileFullPath));
	}
}

camp_html_goto_page($backLink);

?>