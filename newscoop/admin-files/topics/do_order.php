<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/topics/topics_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTopics')) {
	camp_html_display_error($translator->trans("You do not have the right to change topic name.", array(), 'topics'));
	exit;
}

// Get input
$f_position = Input::Get('position', 'array', array());
$f_languages = Input::Get('languages', 'string', '');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())));
	exit;
}

// set position
Topic::UpdateOrder($f_position);

camp_html_add_msg($translator->trans("Topics order saved.", array(), 'topics'), "ok");
camp_html_goto_page("/$ADMIN/topics/");
?>
