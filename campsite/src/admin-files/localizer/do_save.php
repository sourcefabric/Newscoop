<?php
require_once($GLOBALS['g_campsiteDir']."/conf/configuration.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
camp_load_translation_strings("localizer");
require_once('Localizer.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$prefix = Input::Get('prefix', 'string', '', true);
$targetLanguageId = Input::Get('localizer_target_language');
$data = Input::Get('data', 'array');
$result = Localizer::ModifyStrings($prefix, $targetLanguageId, $data);
if (is_array($result)) {
	foreach ($result as $pearError) {
		camp_html_add_msg($pearError->getMessage());
	}
}

header("Location: /$ADMIN/localizer/index.php");
exit;
?>