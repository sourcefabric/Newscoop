<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to modify templates."));
	exit;
}

$f_path = Input::Get('f_path');
$f_old_name = Input::Get('f_old_name');
$fileName = isset($_FILES['f_file']['name']) ? $_FILES['f_file']['name'] : '';

if (!Template::IsValidPath($f_path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}

$backLink = "/$ADMIN/templates/edit_template.php?f_path=" . urlencode($f_path)."&f_name=$f_old_name";

// Check that the mime types match
$oldFilePath = Template::GetFullPath($f_path, $f_old_name);
$oldMimeType = mime_content_type($oldFilePath);
$newMimeType = $_FILES['f_file']['type'];
$equivalentTextTypes = array("text/plain", "text/html", "application/x-php", "application/octet-stream");
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
	camp_html_add_msg(getGS('File "$1" replaced.', $f_old_name), "ok");
} else {
	camp_html_add_msg(getGS("Unable to save the file '$1' to the path '$2'.", $fileName, $f_path) . " "
			. getGS("Please check if the user '$1' has permission to write in this directory.", $Campsite['APACHE_USER']));
}
camp_html_goto_page($backLink);

?>