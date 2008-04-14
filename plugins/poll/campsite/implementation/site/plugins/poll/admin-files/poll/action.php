<?php
// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_poll_code = Input::Get('f_poll_code', 'array');

foreach ($f_poll_code as $code) {
    list($poll_nr, $fk_language_id) = explode('_', $code);
    $poll =& new Poll($fk_language_id, $poll_nr);
    
    switch (Input::Get('f_poll_list_action', 'string')) {
        case 'delete':
            $poll->delete();
        break;
        
        case 'reset':
            $poll->reset();
        break;
    }
}

header('Location: index.php');
?>