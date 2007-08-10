<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/classes/Poll.php");

// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

$poll =& new Poll($f_fk_language_id, $f_poll_nr);
$poll->delete();

header('Location: index.php');
?>