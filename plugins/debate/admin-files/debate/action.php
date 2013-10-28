<?php
$translator = \Zend_Registry::get('container')->getService('translator');
if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}

$f_debate_code = Input::Get('f_debate_code', 'array');

foreach ($f_debate_code as $code) {
    list($debate_nr, $fk_language_id) = explode('_', $code);
    $debate = new Debate($fk_language_id, $debate_nr);

    switch (Input::Get('f_debate_list_action', 'string')) {
        case 'delete':
            $debate->delete();
        break;

        case 'reset':
            $debate->reset();
        break;
    }
}

header('Location: index.php');
?>