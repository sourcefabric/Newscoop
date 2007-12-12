<?php
// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    echo "alert('".(getGS('You do not have the right to manage polls.'))."');";
    exit;
}

$f_target = Input::Get('f_target', 'string');
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

switch ($f_target) {
    case 'publication':
            $PollPublication =& new PollPublication($f_poll_nr, $f_publication_id);
            $PollPublication->$action();
            echo "setOnunload('publication');\n";
    break;
    
    case 'issue':
            $PollIssue =& new PollIssue($f_poll_nr, $f_language_id, $f_issue_nr, $f_publication_id);
            $PollIssue->$action();
            echo "setOnunload('issue');\n";
    break;
    
    case 'section':
            $PollSection =& new PollSection($f_poll_nr, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id);
            $PollSection->$action();
            echo "setOnunload('section');\n";
    break;
    
    case 'article':
            $PollArticle =& new PollArticle($f_poll_nr, $f_language_id, $f_article_nr);
            $PollArticle->$action();
            echo "setOnunload('article');\n";
    break;
}

// this is the ajax response, do not change! Need to exit to avoid output of the menue.
echo "poll_nr = '$f_poll_nr'; action = '$f_action';";
exit;
?>
