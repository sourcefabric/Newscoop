<?php
$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    echo "alert('".$translator->trans('Invalid security token!')."');";
    exit;
}

// Check permissions
if (!$g_user->hasPermission('plugin_poll')) {
    echo "alert('".($translator->trans('You do not have the right to manage polls.', array(), 'plugin_poll'))."');";
    exit;
}

$f_poll_item = Input::Get('f_poll_item', 'string');
$f_language_id = Input::Get('f_language_id', 'int');
$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_nr = Input::Get('f_issue_nr', 'int');
$f_section_nr = Input::Get('f_section_nr', 'int');
$f_article_nr = Input::Get('f_article_nr', 'int');
$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_action = Input::Get('f_action', 'string');

if ($f_action == 'assign') {
    $action = 'create';
} else {
    $action = 'delete';
}

switch ($f_poll_item) {
    case 'publication':
            $PollPublication = new PollPublication($f_poll_nr, $f_publication_id);
            if ($PollPublication->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment.', array(), 'plugin_poll').'");';
            }
    break;

    case 'issue':
            $PollIssue = new PollIssue($f_poll_nr, $f_language_id, $f_issue_nr, $f_publication_id);
            if ($PollIssue->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment.', array(), 'plugin_poll').'");';
            }
    break;

    case 'section':
            $PollSection = new PollSection($f_poll_nr, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id);
            if ($PollSection->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment.', array(), 'plugin_poll').'");';
            }
    break;

    case 'article':
            $PollArticle = new PollArticle($f_poll_nr, $f_language_id, $f_article_nr);
            if ($PollArticle->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment', array(), 'plugin_poll').'");';
            }
    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
