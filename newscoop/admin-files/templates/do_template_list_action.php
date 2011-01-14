<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Get input
$f_template_codes = Input::Get('f_template_code', 'array', array(), true);
$f_template_list_action = Input::Get('f_template_list_action');
if (sizeof($f_template_codes) == 0) {
	camp_html_add_msg('You must select at least one template to perform an action.');
	camp_html_goto_page("/$ADMIN/templates/index.php");
	exit(0);
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

// Validate permissions
switch ($f_template_list_action) {
case "move":
	if (!$g_user->hasPermission('ManageTempl')) {
		camp_html_display_error(getGS("You do not have the right to move templates."));
		exit;
	}
	break;
case "delete":
	if (!$g_user->hasPermission('DeleteTempl')) {
		camp_html_display_error(getGS("You do not have the right to delete templates."));
		exit;
	}
	break;
}

$templateCodes = $f_template_codes;

switch ($f_template_list_action) {
case "delete":
	$anyDeleted = 0;
	$anyInUse = 0;
	foreach ($templateCodes as $templateCode) {
		$templateObj = new Template($templateCode);
		$deleted = $templateObj->delete();
		if ($deleted == true) {
			$anyDeleted = 1;
		} elseif (!$anyInUse) {
			$anyInUse = 1;
		}
	}
	if ($anyDeleted == 1) {
	        // Clear compiled templates
	        require_once($GLOBALS['g_campsiteDir']."/template_engine/classes/CampTemplate.php");
		CampTemplate::singleton()->clear_compiled_tpl();
		camp_html_add_msg(getGS("Template(s) deleted."), "ok");
	}
	if ($anyInUse == 1) {
		camp_html_add_msg(getGS("Some templates could not be deleted because they are in use."));
	}
	break;
case "move":
	$f_current_folder = Input::Get('f_current_folder', 'string', 0, true);
	$argsStr = 'f_action=move&f_current_folder='.urlencode($f_current_folder);
	foreach ($templateCodes as $templateCode) {
		$argsStr .= "&f_template_code[]=$templateCode";
	}
	camp_html_goto_page("/$ADMIN/templates/move.php?".$argsStr);
}

camp_html_goto_page("/$ADMIN/templates/index.php");
?>
