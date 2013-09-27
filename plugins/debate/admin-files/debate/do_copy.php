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

$f_debate_nr = Input::Get('f_debate_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');
$f_answers = Input::Get('f_answer', 'array');
$f_copy_statistics = Input::Get('f_copy_statistics', 'boolean');

$data = array(
    'title' => Input::Get('f_title', 'string'),
    'question' => Input::Get('f_question', 'string'),
    'date_begin' => Input::Get('f_date_begin', 'string'),
    'date_end' => Input::Get('f_date_end', 'string'),
    'votes_per_user' => Input::Get('f_votes_per_user', 'int'),
);

foreach ($f_answers as $answer) {
    if (isset($answer['number']) && !empty($answer['number']) && strlen($answer['text'])) {
        $DebateAnswer = new DebateAnswer($f_fk_language_id, $f_debate_nr, $answer['number']);
        $answers[] = array(
            'number' => $answer['number'],
            'text' => $answer['text'],
            'nr_of_votes' => $f_copy_statistics ? $DebateAnswer->getProperty('nr_of_votes') : 0,
            'value' => $f_copy_statistics ? $DebateAnswer->getProperty('value') : 0,
        );
    }
}

$source = new Debate($f_fk_language_id, $f_debate_nr);
$copy = $source->createCopy($data, $answers);

/*
foreach($translation->getAnswers() as $answer) {
    $answer->setProperty('answer', $f_answers[$answer->getNumber()]);
}
*/

header("Location: index.php");
exit;