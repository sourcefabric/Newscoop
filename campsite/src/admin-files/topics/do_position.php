<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/topics/topics_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTopics')) {
	camp_html_display_error(getGS("You do not have the right to change topic name."));
	exit;
}

// Get input
$f_topic_number = Input::Get('f_topic_number', 'int', 0);
$f_move = Input::Get('f_move', 'string', 'up_rel');
$f_position = Input::Get('f_position', 'int', 1, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

$topicObj = new Topic($f_topic_number);
if (!$topicObj->exists()) {
	camp_html_display_error(getGS('Topic does not exist.'));
	exit;
}

switch ($f_move) {
case 'up_rel':
	$topicObj->positionRelative('up', 1);
	break;
case 'down_rel':
	$topicObj->positionRelative('down', 1);
	break;
case 'abs':
	$r = $topicObj->positionAbsolute($f_position);
	break;
default: ;
}


camp_html_add_msg(getGS("Topics order changed."), "ok");
camp_html_goto_page("/$ADMIN/topics/");
?>
