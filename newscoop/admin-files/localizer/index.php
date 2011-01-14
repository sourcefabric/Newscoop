<?php
require_once($GLOBALS['g_campsiteDir']."/conf/configuration.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Input.php");
camp_load_translation_strings("localizer");
require_once('Localizer.php');

global $g_translationStrings;
global $g_localizerConfig;

// Check permissions
if (!$g_user->hasPermission('ManageLocalizer')) {
	camp_html_display_error(getGS("You do not have the right to manage the localizer."));
	exit;
}

$crumbs = array();
$crumbs[] = array("Configure", "");
$crumbs[] = array("Localizer", "");
echo camp_html_breadcrumbs($crumbs);

require_once("translate.php");
translationForm($_REQUEST);

?>
