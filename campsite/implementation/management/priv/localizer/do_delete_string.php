<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("localizer");
require_once('Localizer.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$prefix = Input::Get('prefix', 'string', '', true);
$deleteMe = Input::Get('string', 'string');
Localizer::RemoveString($prefix, $deleteMe);

header("Location: /$ADMIN/localizer/index.php");
exit;
?>