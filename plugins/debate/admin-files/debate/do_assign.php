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

$f_target = Input::Get('f_target', 'string');
$f_language_id = Input::Get('f_language_id', 'int');
$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_nr = Input::Get('f_issue_nr', 'int');
$f_section_nr = Input::Get('f_section_nr', 'int');
$f_article_nr = Input::Get('f_article_nr', 'int');

$f_debate_exists = Input::Get('f_debate_exists', 'array', array());
$f_debate_checked = Input::Get('f_debate_checked', 'array', array());

$p_a = 0;
$p_u = 0;

switch ($f_target) {
    case 'publication':
        foreach ($f_debate_exists as $debate_nr => $lost) {
            $DebatePublication = new DebatePublication($debate_nr, $f_publication_id);

            if (array_key_exists($debate_nr, $f_debate_checked) && !$DebatePublication->exists()) {
                $DebatePublication->create();
                $p_a++;
            } elseif (!array_key_exists($debate_nr, $f_debate_checked) && $DebatePublication->exists()) {
                $DebatePublication->delete();
                $p_u++;
            }
        }
        ?>
        <script>
        try {
        window.opener.document.forms[0].onsubmit();
        window.opener.document.forms[0].submit();
        } catch (e) {}
        window.close();
        </script>
        <?php
    break;

    case 'issue':
        foreach ($f_debate_exists as $debate_nr => $lost) {
            $DebateIssue = new DebateIssue($debate_nr, $f_language_id, $f_issue_nr, $f_publication_id);
            $x = $DebateIssue->exists();
            if (array_key_exists($debate_nr, $f_debate_checked) && !$DebateIssue->exists()) {
                $DebateIssue->create();
                $p_a++;
            } elseif (!array_key_exists($debate_nr, $f_debate_checked) && $DebateIssue->exists()) {
                $DebateIssue->delete();
                $p_u++;
            }
        }
        ?>
        <script>
        try {
        window.opener.document.forms['issue_edit'].onsubmit();
        window.opener.document.forms['issue_edit'].submit();
        } catch (e) {}
        window.close();
        </script>
        <?php
    break;

    case 'section':
        foreach ($f_debate_exists as $debate_nr => $val) {
            $DebateSection = new DebateSection($debate_nr, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id);

            if (array_key_exists($debate_nr, $f_debate_checked) && !$DebateSection->exists()) {
                $DebateSection->create();
                $a++;
            } elseif (!array_key_exists($debate_nr, $f_debate_checked) && $DebateSection->exists()) {
                $DebateSection->delete();
                $u++;
            }
        }
        ?>
        <script>
        try {
        window.opener.document.forms['section_edit'].onsubmit();
        window.opener.document.forms['section_edit'].submit();
        } catch (e) {}
        window.close();
        </script>
        <?php
    break;

    case 'article':
        foreach ($f_debate_exists as $debate_nr => $val) {
            $DebateArticle = new DebateArticle($debate_nr, $f_language_id, $f_article_nr);

            if (array_key_exists($debate_nr, $f_debate_checked) && !$DebateArticle->exists()) {
                $DebateArticle->create();
                $p_a++;
            } elseif (!array_key_exists($debate_nr, $f_debate_checked) && $DebateArticle->exists()) {
                $DebateArticle->delete();
                $p_u++;
            }
        }
        ?>
        <script>
        try {
        //window.opener.document.forms['article_edit'].f_message.value = "<?php echo $translator->trans("$1/$2 debates assigned/unassigned.", $p_a, $p_u); ?>";
        window.opener.document.forms['article_edit'].onsubmit();
        window.opener.document.forms['article_edit'].submit();
        } catch (e) {}
        window.close();
        </script>
        <?php
    break;

    default:
	   camp_html_display_error($translator->trans('Invalid input'), 'javascript: window.close()');
	   exit;
    break;
}
exit;