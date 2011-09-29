<?php
camp_load_translation_strings("plugin_debate");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error(getGS('You do not have the right to manage debates.'));
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