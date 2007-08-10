<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/classes/Poll.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/classes/PollQuestion.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/classes/PollAnswer.php");

// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_poll_id = Input::Get('f_poll_id', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

$f_title = Input::Get('f_title', 'string');
$f_question = Input::Get('f_question', 'string');
$f_date_begin = Input::Get('f_date_begin', 'string');
$f_date_end = Input::Get('f_date_end', 'string');
$f_show_after_expiration = Input::Get('f_show_after_expiration', 'boolean');
$f_nr_of_answers = Input::Get('f_nr_of_answers', 'int');

$f_answers = Input::Get('f_answer', 'array');

if ($f_poll_id) {
    // update existing poll   
    
} else {
    // create new poll
    $poll =& new Poll($f_fk_language_id);   
    $success = $poll->create($f_title, $f_question, $f_date_begin, $f_date_end, $f_nr_of_answers, $f_show_after_expiration);
    
    if ($success) {
        foreach ($f_answers as $nr_answer => $text) {
            if ($text !== '__undefined__') {
                $answer =& new PollAnswer($f_fk_language_id, $poll->getNumber(), $nr_answer);
                $success = $answer->create($text);
            }
        }            
    }
}
header('Location: index.php');
?>