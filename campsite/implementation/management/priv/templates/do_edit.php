<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($Path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}
$Name = Input::Get('Name', 'string', '');
$cField = Input::Get('cField', 'string', '');
$nField = str_replace("\\r", "\r", $cField);
$nField = str_replace("\\n", "\n", $nField);

$filename = Template::GetFullPath($Path, $Name);

$result = false;
if (file_exists($filename)) {
	if (is_writable($filename)) {
		if (@$handle = fopen($filename, 'w')) {
			$result = fwrite($handle, $nField);
			fclose($handle);
		}
	} else {
		camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_FILE, $filename));
	}
} else {
	camp_html_add_msg(getGS("Template $1 no longer exists!", "&quot;".$filename."&quot;"));
	camp_html_goto_page("/$ADMIN/templates/?Path=".urlencode($Path));
}

if ($result !== false) {
	$logtext = getGS('Template $1 was changed', $Path."/".$Name);
	Log::Message($logtext, $g_user->getUserName(), 113);
	camp_html_add_msg(getGS("The template '$1' was saved successfully.", $Name), "ok");
}
camp_html_goto_page("/$ADMIN/templates/edit_template.php?"
	."f_path=".urlencode($Path)
	."&f_name=".urlencode($Name));
?>