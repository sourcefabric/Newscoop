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
$missingStrings = Localizer::FindMissingStrings($prefix);
if (count($missingStrings) > 0) {
    $result = Localizer::AddStringAtPosition($prefix, 0, $missingStrings);
    if (PEAR::isError($result)) {
    	camp_html_add_msg($result->getMessage());
    }
}

header("Location: /$ADMIN/localizer/index.php");
exit;
?>