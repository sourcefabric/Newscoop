<?php
if (!$g_user->hasPermission("ManagePoll")) {
	camp_html_display_error(getGS("You do not have the right to manage poll."));
	exit;
}

$f_target = Input::Get('f_target', 'string');
$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_nr = Input::Get('f_issue_nr', 'int');
$f_issue_language_id = Input::Get('f_issue_language_id', 'int');
$f_section_nr = Input::Get('f_section_nr', 'int');
$f_section_language_id = Input::Get('f_section_language_id', 'int');
$f_article_nr = Input::Get('f_article_nr', 'int');
$f_article_language_id = Input::Get('f_article_language_id', 'int');

$assigned = array();

switch ($f_target) {
    case 'publication':
        foreach (PollPublication::GetAssignments(null, null, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getPollNumber()][$assignObj->getPollLanguageId()] = true;   
        }
    break;
    
    case 'issue':
        foreach (PollIssue::GetAssignments(null, null, $f_issue_language_id, $f_issue_nr, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getPollNumber()][$assignObj->getPollLanguageId()] = true;   
        }
    break;
    
    case 'section':
        foreach (PollSection::GetAssignments(null, null, $f_section_language_id, $f_section_nr, $f_issue_nr, $f_publication_id) as $assignObj) {
            $assigned[$assignObj->getPollNumber()][$assignObj->getPollLanguageId()] = true;   
        }
    break;
    
    case 'article':
        foreach (PollArticle::GetAssignments(null, null, $f_article_language_id, $f_article_number) as $assignObj) {
            $assigned[$assignObj->getPollNumber()][$assignObj->getPollLanguageId()] = true;   
        }
    break;
    
    default:
	   camp_html_display_error(getGS('Invalid input'), 'javascript: window.close()');
	   exit;
    break; 
}

?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Assign poll"); ?></title>
	<?php include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php"); ?>
</head>
<body>

<?php camp_html_display_msgs(); ?>

<FORM NAME="assign_poll" METHOD="POST" ACTION="do_assign.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input" style="margin-top: 10px;" width="90%" height="90%">
<TR>
	<TD COLSPAN="2">
		<B><?php putGS("Assign poll"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php echo $f_publication_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_nr" VALUE="<?php echo $f_issue_nr; ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_language_id" VALUE="<?php echo $f_issue_language_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_section_nr" VALUE="<?php echo $f_section_nr; ?>">
<INPUT TYPE="HIDDEN" NAME="f_section_language_id" VALUE="<?php echo $f_section_language_id; ?>">
<INPUT TYPE="HIDDEN" NAME="f_article_nr" VALUE="<?php echo $f_article_nr; ?>">
<INPUT TYPE="HIDDEN" NAME="f_article_language_id" VALUE="<?php echo $f_article_language_id; ?>">
<INPUT type="hidden" name="f_target" value="<?php echo $f_target; ?>">

<?php   
foreach (Poll::getPolls() as $poll) {
    ?><tr><td width="100%"><?php
    $poll_nr = $poll->getNumber();
    $language_id = $poll->getLanguageId();
    $poll_name = $poll->getName();
    $language_name = $poll->getLanguageName();
     
    if (array_key_exists($poll_nr, $assigned)) {
        if (array_key_exists($language_id, $assigned[$poll_nr])) {
            p('<input type="checkbox" name="f_poll_checked['.$poll_nr.'_'.$language_id.']" checked="checked" />'); 
        } else {
            p('<input type="checkbox" name="f_poll_checked['.$poll_nr.'_'.$language_id.']" />');   
        }
    } else {
        p('<input type="checkbox" name="f_poll_checked['.$poll_nr.'_'.$language_id.']" />');
    }
    
    p("$poll_name ($language_name)");
    p('<input type="hidden" name="f_poll_exists['.$poll_nr.'_'.$language_id.']" value="1" />');
    
    ?></td></tr><?php
}
?>

<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	&nbsp;&nbsp;
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" onclick="window.close();">
	</TD>
</TR>
</TABLE>
</FORM>
</P>

</body>
</html>
