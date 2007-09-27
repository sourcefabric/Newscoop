<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
camp_load_translation_strings("localizer");
require_once('Localizer.php');

// Check permissions
if (!$g_user->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$prefix = Input::Get('prefix', 'string', '', true);
$pos1 = Input::Get('pos1', 'int');
$pos2 = Input::Get('pos2', 'int');
Localizer::RepositionString($prefix, $pos1, $pos2);

header("Location: /$ADMIN/localizer/index.php");
exit;
?>