<?php
$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('plugin_poll')) {
    camp_html_display_error($translator->trans('You do not have the right to manage polls.', array(), 'plugin_poll'));
    exit;
}

$f_poll_code = Input::Get('f_poll_code', 'array');

foreach ($f_poll_code as $code) {
    list($poll_nr, $fk_language_id) = explode('_', $code);
    $poll = new Poll($fk_language_id, $poll_nr);

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