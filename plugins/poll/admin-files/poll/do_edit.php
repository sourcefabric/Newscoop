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

$f_title = Input::Get('f_title', 'string');
$f_question = Input::Get('f_question', 'string');
$f_date_begin = Input::Get('f_date_begin', 'string');
$f_date_end = Input::Get('f_date_end', 'string');
$f_votes_per_user = Input::Get('f_votes_per_user', 'int');
$f_is_extended = Input::Get('f_is_extended', 'boolean');
$f_nr_of_answers = Input::Get('f_nr_of_answers', 'int');

$f_answers = Input::Get('f_answer', 'array');
$f_onhitlist = Input::Get('f_onhitlist', 'array');

$poll = new Poll($f_fk_language_id, $f_poll_nr);

if ($poll->exists()) {
    // update existing poll
    $poll = new Poll($f_fk_language_id, $f_poll_nr);
    $poll->setProperty('title', $f_title);
    $poll->setProperty('question', $f_question);
    $poll->setProperty('date_begin', $f_date_begin);
    $poll->setProperty('date_end', $f_date_end);
    $poll->setProperty('votes_per_user', $f_votes_per_user);
    $poll->setProperty('nr_of_answers', $f_nr_of_answers);
    $poll->setProperty('is_extended', $f_is_extended);

    foreach ($f_answers as $nr_answer => $text) {
        if ($text !== '__undefined__') {
            $answer = new PollAnswer($f_fk_language_id, $f_poll_nr, $nr_answer);
            if ($answer->exists()) {
                $answer->setProperty('answer', $text);
            } else {
                $answer->create($text);
            }
        }
    }

    PollAnswer::SyncNrOfAnswers($f_fk_language_id, $f_poll_nr);

} else {
    // create new poll
    $poll = new Poll($f_fk_language_id);
    $success = $poll->create($f_title, $f_question, $f_date_begin, $f_date_end, $f_nr_of_answers, $f_votes_per_user);

    if ($success) {
        $poll->setProperty('is_extended', $f_is_extended);

        foreach ($f_answers as $nr_answer => $text) {
            if ($text !== '__undefined__') {
                $answer = new PollAnswer($f_fk_language_id, $poll->getNumber(), $nr_answer);
                $success = $answer->create($text);
            }
        }
    }
}
$f_from = Input::Get('f_from', 'string', 'index.php');
camp_html_goto_page($f_from);
?>