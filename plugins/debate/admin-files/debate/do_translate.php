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

$f_debate_nr = Input::Get('f_debate_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');
$f_target_language_id = Input::Get('f_target_language_id', 'int');

$f_title = Input::Get('f_title', 'string');
$f_question = Input::Get('f_question', 'string');

$f_answers = Input::Get('f_answer', 'array');

$Source = new Debate($f_fk_language_id, $f_debate_nr);
$Translation = $Source->createTranslation($f_target_language_id, $f_title, $f_question);

foreach($Translation->getAnswers() as $answer) {
    $answer->setProperty('answer', $f_answers[$answer->getNumber()]);
}

header("Location: index.php");
exit;