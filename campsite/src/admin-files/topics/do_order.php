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
$f_position = Input::Get('position', 'array', array());

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

// set position
$order_ary = array();
foreach ($f_position as $subtree) {
    foreach ($subtree as $topicId => $position) {
        $order_ary[$topicId] = $position;
    }
}
Topic::UpdateOrder($order_ary);

camp_html_add_msg(getGS("Topics order saved."), "ok");
camp_html_goto_page("/$ADMIN/topics/");
?>
