<?php
$translator = \Zend_Registry::get('container')->getService('translator');
if (!SecurityToken::isValid()) {
    echo "alert('".$translator->trans('Invalid security token!')."');";
    exit;
}

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    echo "alert('".($translator->trans('You do not have the right to manage debates.'))."');";
    exit;
}

$f_debate_item = Input::Get('f_debate_item', 'string');
$f_language_id = Input::Get('f_language_id', 'int');
$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_nr = Input::Get('f_issue_nr', 'int');
$f_section_nr = Input::Get('f_section_nr', 'int');
$f_article_nr = Input::Get('f_article_nr', 'int');
$f_debate_nr = Input::Get('f_debate_nr', 'int');
$f_action = Input::Get('f_action', 'string');

if ($f_action == 'assign') {
    $action = 'create';
} else {
    $action = 'delete';
}

switch ($f_debate_item) {
    case 'publication':
            $DebatePublication = new DebatePublication($f_debate_nr, $f_publication_id);
            if ($DebatePublication->$action()) {
                echo "debate_nr = '$f_debate_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment.', array(), 'plugin_debate').'");';
            }
    break;

    case 'issue':
            $DebateIssue = new DebateIssue($f_debate_nr, $f_language_id, $f_issue_nr, $f_publication_id);
            if ($DebateIssue->$action()) {
                echo "debate_nr = '$f_debate_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment.', array(), 'plugin_debate').'");';
            }
    break;

    case 'section':
            $DebateSection = new DebateSection($f_debate_nr, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id);
            if ($DebateSection->$action()) {
                echo "debate_nr = '$f_debate_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment.', array(), 'plugin_debate').'");';
            }
    break;

    case 'article':
            $DebateArticle = new DebateArticle($f_debate_nr, $f_language_id, $f_article_nr);
            if ($DebateArticle->$action()) {
                echo "debate_nr = '$f_debate_nr'; action = '$f_action';";
            } else {
                echo 'alert("'.$translator->trans('Error changing attachment', array(), 'plugin_debate').'");';
            }
    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
