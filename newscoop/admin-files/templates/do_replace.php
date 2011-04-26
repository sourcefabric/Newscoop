<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$f_path = Input::Get('f_path');
$f_old_name = Input::Get('f_old_name');
$fileName = isset($_FILES['f_file']['name']) ? $_FILES['f_file']['name'] : '';

if (!Template::IsValidPath($f_path) || !Template::IsValidPath($f_path.DIR_SEP.$f_old_name)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

$backLink = "/$ADMIN/templates/edit_template.php?f_path=" . urlencode($f_path)."&f_name=$f_old_name";

// Check that the mime types match
$oldFilePath = Template::GetFullPath($f_path, $f_old_name);
$oldMimeType = (function_exists('mime_content_type')) ? mime_content_type($oldFilePath) :
							camp_mime_content_type($oldFilePath);
$oldRelativeFilePath = (!empty($f_path)) ? ltrim($f_path.DIR_SEP.$f_old_name, '/') : $f_old_name;
$newMimeType = $_FILES['f_file']['type'];
$equivalentTextTypes = array("text/plain", "text/html", "application/x-php", "application/octet-stream", "application/javascript", "text/x-c", "text/css" , "text/x-php", "application/x-httpd-php", "text/x-c++");
$matched = false;
if (in_array($oldMimeType, $equivalentTextTypes) && in_array($newMimeType, $equivalentTextTypes)) {
	$matched = true;
}
if (!$matched && ($oldMimeType != $newMimeType)) {
	camp_html_add_msg(getGS('You can only replace a file with a file of the same type.  The original file is of type "$1", and the file you uploaded was of type "$2".', $oldMimeType, $newMimeType));
	camp_html_goto_page($backLink);
}

// Move the new file it its place
$success = Template::OnUpload("f_file", $f_path, $f_old_name);
if ($success) {
        // Clear compiled template
        require_once($GLOBALS['g_campsiteDir']."/template_engine/classes/CampTemplate.php");
        CampTemplate::singleton()->clear_compiled_tpl($oldRelativeFilePath);

	camp_html_add_msg(getGS('File "$1" replaced.', $f_old_name), "ok");
} else {
	camp_html_add_msg(getGS("Unable to save the file '$1' to the path '$2'.", $fileName, $f_path) . " "
			. getGS("Please check if the user '$1' has permission to write in this directory.", $Campsite['APACHE_USER']));
}
camp_html_goto_page($backLink);

?>
