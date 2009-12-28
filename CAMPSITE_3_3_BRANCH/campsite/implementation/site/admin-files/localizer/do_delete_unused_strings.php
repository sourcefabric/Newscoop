<?php
require_once($GLOBALS['g_campsiteDir']."/conf/configuration.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
camp_load_translation_strings("localizer");
require_once('Localizer.php');

// Check permissions
if (!$g_user->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$prefix = Input::Get('prefix', 'string', '', true);
$unusedStrings = Localizer::FindUnusedStrings($prefix);
if (count($unusedStrings) > 0) {
   	Localizer::RemoveString($prefix, $unusedStrings);
}

header("Location: /$ADMIN/localizer/index.php");
exit;
?>