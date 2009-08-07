<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('DeleteTempl')) {
	camp_html_display_error(getGS("You do not have the right to delete templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
$Name = Input::Get('Name', 'string', '');
$isFile = Input::Get('What', 'int', 0);

$Path = preg_replace('#/+#', '/', $Path);

if (!Template::IsValidPath($Path.DIR_SEP.$Name)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

$backLink = "/$ADMIN/templates/?Path=".urlencode($Path);
$fileFullName = preg_replace('#^/+#', '', (!empty($Path)) ? $Path.DIR_SEP.$Name : $Name);
$fileFullPath = Template::GetFullPath($Path, '');
$errorMsgs = array();


$deleted = false;
if (!$isFile) {
        $deleted = rmdir($fileFullPath.$Name);
	if ($deleted) {
		$logtext = getGS('Directory $1 was deleted', mysql_real_escape_string($fileFullName));
		Log::Message($logtext, $g_user->getUserId(), 112);
		camp_html_add_msg($logtext, "ok");
	} else {
		camp_html_add_msg(camp_get_error_message(CAMP_ERROR_RMDIR, $fileFullPath));
	}
} else {
	$inUse = Template::InUse($fileFullName);
	if ($inUse == CAMP_ERROR_READ_FILE || $inUse == CAMP_ERROR_READ_DIR) {
                camp_html_add_msg(getGS("There are some files which can not be readed so Campsite was not able to determine whether '$1' is in use or not. Please fix this, then try to delete the template again.", basename($fileFullName)));
	} elseif ($inUse == false) {
	        $template = new Template($fileFullName);
		if ($template->exists() && $template->delete()) {
			$logtext = getGS('Template object $1 was deleted', mysql_real_escape_string($fileFullName));
			Log::Message($logtext, $g_user->getUserId(), 112);
			camp_html_add_msg($logtext, "ok");
		} else {
			camp_html_add_msg(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $fileFullName));
		}
	} else {
		camp_html_add_msg(getGS("The template object $1 is in use and can not be deleted.", $fileFullName));
	}
}

camp_html_goto_page($backLink);

?>
