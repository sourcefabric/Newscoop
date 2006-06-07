<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
camp_load_translation_strings("localizer");
require_once('Localizer.php');

// Check permissions
if (!$g_user->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$prefix = Input::Get('prefix', 'string', '', true);
$newPrefix = Input::Get('new_prefix');
$moveStr = Input::Get('string');
Localizer::ChangeStringPrefix($prefix, $newPrefix, $moveStr);

header("Location: /$ADMIN/localizer/index.php");
exit;
?>