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

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');
$f_target_language_id = Input::Get('f_target_language_id', 'int');

$f_title = Input::Get('f_title', 'string');
$f_question = Input::Get('f_question', 'string');

$f_answers = Input::Get('f_answer', 'array');

$Source = new Poll($f_fk_language_id, $f_poll_nr);
$Translation = $Source->createTranslation($f_target_language_id, $f_title, $f_question);

foreach($Translation->getAnswers() as $answer) {
    $answer->setProperty('answer', $f_answers[$answer->getNumber()]);
}

header("Location: index.php");
?>