<?php
// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');
$f_target_language_id = Input::Get('f_target_language_id', 'int');
$f_title = Input::Get('f_title', 'string');
$f_question = Input::Get('f_question', 'string');
$f_answers = Input::Get('f_answer', 'array');
$f_is_used_as_default = Input::Get('f_is_used_as_default', 'boolean');

$source = new Poll($f_fk_language_id, $f_poll_nr);
$translation = $source->createTranslation($f_target_language_id, $f_title, $f_question, $f_is_used_as_default);

foreach($translation->getAnswers() as $answer) {
    $answer->setProperty('answer', $f_answers[$answer->getNumber()]);   
}

header("Location: index.php");
?>