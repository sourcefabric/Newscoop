<?php
camp_load_translation_strings("plugin_poll");

// Check permissions
if (!$g_user->hasPermission('plugin_poll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
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
            $PollPublication =& new PollPublication($f_poll_nr, $f_publication_id);
            if ($PollPublication->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";    
            } else {
                echo 'alert("'.getGS('Error changing attachment.').'");';   
            }
    break;
    
    case 'issue':
            $PollIssue =& new PollIssue($f_poll_nr, $f_language_id, $f_issue_nr, $f_publication_id);
            if ($PollIssue->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";    
            } else {
                echo 'alert("'.getGS('Error changing attachment.').'");';   
            }
    break;
    
    case 'section':
            $PollSection =& new PollSection($f_poll_nr, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id);
            if ($PollSection->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";    
            } else {
                echo 'alert("'.getGS('Error changing attachment.').'");';   
            }
    break;
    
    case 'article':
            $PollArticle =& new PollArticle($f_poll_nr, $f_language_id, $f_article_nr);
            if ($PollArticle->$action()) {
                echo "poll_nr = '$f_poll_nr'; action = '$f_action';";    
            } else {
                echo 'alert("'.getGS('Error changing attachment').'");';   
            }
    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
