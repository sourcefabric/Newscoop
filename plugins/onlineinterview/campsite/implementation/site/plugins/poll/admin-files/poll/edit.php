<?php
// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$allLanguages = Language::GetLanguages();

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

if ($f_poll_nr && $f_fk_language_id) {
    $poll = new Poll($f_fk_language_id, $f_poll_nr, true);
    
    if ($poll->exists()) {  
        $poll_nr = $poll->getNumber(); 
        $title = $poll->getProperty('title');
        $question = $poll->getProperty('question');
        $date_begin = $poll->getProperty('date_begin');
        $date_end = $poll->getProperty('date_end');
        $nr_of_answers = $poll->getProperty('nr_of_answers');
        $fk_language_id = $poll->getProperty('fk_language_id');
        $is_show_after_expiration = $poll->getProperty('is_show_after_expiration');
        $is_used_as_default = $poll->getProperty('is_used_as_default');
        
        $poll_answers = $poll->getAnswers();
        foreach ($poll_answers as $poll_answer) {
            $answers[$poll_answer->getProperty('nr_answer')] = $poll_answer->getProperty('answer');   
        }
    }
}

/*
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
                  'Section' => $sectionObj);
camp_html_content_top(getGS('Add new article'), $topArray, true, false, array(getGS("Articles") => "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_fk_language_id=$f_fk_language_id"));
*/
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
    <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
    <TD><A HREF="index.php"><B><?php  putGS("Poll List"); ?></B></A></TD>
</TR>
</TABLE>

<?php
include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");
camp_html_display_msgs();
?>
<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-system.css);</style>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/lang/calendar-<?php echo camp_session_get('TOL_Language', 'en'); ?>.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-setup.js"></script>

<P>
<FORM NAME="edit_poll" METHOD="POST" ACTION="do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php if ($poll) { ?>
<INPUT TYPE="HIDDEN" NAME="f_poll_nr" VALUE="<?php p($poll->getNumber()); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <TD COLSPAN="2">
        <B><?php  if ($poll) putGS("Edit Poll"); else putGS('Add new Poll'); ?></B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <td valign="top">
        <table>
          <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
            <TD style="padding-top: 3px;">
                <?php if (count($allLanguages) > 1) { ?>
                <SELECT NAME="f_fk_language_id" alt="select" emsg="<?php putGS("You must select a language.")?>" class="input_select">
                <option value="0"><?php putGS("---Select language---"); ?></option>
                <?php
                 foreach ($allLanguages as $tmpLanguage) {
                     camp_html_select_option($tmpLanguage->getLanguageId(),
                                             $fk_language_id,
                                             $tmpLanguage->getNativeName());
                }
                ?>
                </SELECT>
                <?php } else {
                    $tmpLanguage = array_pop($allLanguages);
                    echo '<b>'.htmlspecialchars($tmpLanguage->getNativeName()).'</b>';
                    ?>
                    <input type="hidden" name="f_article_language" value="<?php p($tmpLanguage->getLanguageId()); ?>">
                    <?php
                }
                ?>

            </TD>
        </TR>
          <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Date begin"); ?>:</TD>
            <TD>
                <?php $now = getdate(); ?>
                <table cellpadding="0" cellspacing="2"><tr>
                    <td><INPUT TYPE="TEXT" class="input_text" NAME="f_date_begin" id="f_date_begin" maxlength="10" SIZE="11" VALUE="<?php p($date_begin); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must complete the $1 field.',"'".getGS('Date begin')."'"); ?>"></td>
                    <td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_c"
                         style="cursor: pointer; border: 1px solid red;"
                          title="Date selector"
                          onmouseover="this.style.background='red';"
                          onmouseout="this.style.background=''" /></td>
                      <td><?php putGS('YYYY-MM-DD'); ?></td>
                </tr></table>
                <script type="text/javascript">
                    Calendar.setup({
                        inputField:"f_date_begin",
                        ifFormat:"%Y-%m-%d",
                        showsTime:false,
                        showOthers:true,
                        weekNumbers:false,
                        range:new Array(<?php p($now["year"]); ?>, 2020),
                        button:"f_trigger_c"
                    });
                </script>

            </TD>
        </TR>
        <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
            <TD>
                <?php $now = getdate(); ?>
                <table cellpadding="0" cellspacing="2"><tr>
                    <td><INPUT TYPE="TEXT" class="input_text" NAME="f_date_end" id="f_date_end" maxlength="10" SIZE="11" VALUE="<?php p($date_end); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must complete the $1 field.',"'".getGS('Date end')."'"); ?>"></td>
                    <td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_c"
                         style="cursor: pointer; border: 1px solid red;"
                          title="Date selector"
                          onmouseover="this.style.background='red';"
                          onmouseout="this.style.background=''" /></td>
                      <td><?php putGS('YYYY-MM-DD'); ?></td>
                </tr></table>
                <script type="text/javascript">
                    Calendar.setup({
                        inputField:"f_date_end",
                        ifFormat:"%Y-%m-%d",
                        showsTime:false,
                        showOthers:true,
                        weekNumbers:false,
                        range:new Array(<?php p($now["year"]); ?>, 2020),
                        button:"f_trigger_c"
                    });
                </script>

            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Show after expiration"); ?>:</TD>
            <TD>
            <INPUT TYPE="checkbox" NAME="f_show_after_expiration" class="input_checkbox" value="1" <?php $is_show_after_expiration ? p('checked') : null; ?> >
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Used as default"); ?>:</TD>
            <TD>
            <INPUT TYPE="checkbox" NAME="f_is_used_as_default" class="input_checkbox" value="1" <?php $is_used_as_default ? p('checked') : null; ?> >
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Title"); ?>:</TD>
            <TD>
            <INPUT TYPE="TEXT" NAME="f_title" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Title')); ?>" value="<?php echo htmlspecialchars($title); ?>">
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Question"); ?>:</TD>
            <TD>
            <INPUT TYPE="TEXT" NAME="f_question" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Question')); ?>" value="<?php echo htmlspecialchars($question); ?>">
            </TD>
        </TR>
        <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Number of answers"); ?>:</TD>
            <TD style="padding-top: 3px;">
                <?php if (count($allLanguages) > 1) { ?>
                <SELECT NAME="f_nr_of_answers" alt="select" emsg="<?php putGS("You must select number of answers.")?>" class="input_select" onchange="poll_set_nr_of_answers()">
                <option value="0"><?php putGS("---Select---"); ?></option>
                <?php
                 for($n=2; $n<20; $n++) {
                     camp_html_select_option($n,
                                             $nr_of_answers,
                                             $n);
                }
                ?>
                </SELECT>
            </TD>
        </TR>
        
        <?php
        for ($n=1; $n<=20; $n++) {
            ?>
            <tr id="poll_answer_tr_<?php p($n); ?>" style="display: <?php $nr_of_answers >= $n ? p('table-row') : p('none'); ?>">
                <TD ALIGN="RIGHT" ><?php  putGS("Answer $1", $n); ?>:</TD>
                <TD>
                <INPUT TYPE="TEXT" NAME="f_answer[<?php p($n); ?>]" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" id="poll_answer_input_<?php p($n); ?>" emsg="<?php putGS('You must complete the $1 field.', getGS('Answer $1', $n)); ?>" value="<?php isset($answers[$n]) ? p(htmlspecialchars($answers[$n])) : p('__undefined__'); ?>">
                </TD>
            </TR>
            <?php
        }
        ?>
        
        </table>
    </td>
</tr>
<TR>
    <TD COLSPAN="2" align="center">
        <HR NOSHADE SIZE="1" COLOR="BLACK">
        <INPUT TYPE="submit" NAME="save" VALUE="<?php  putGS('Save'); ?>" class="button">
    </TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.edit_poll.f_title.focus();

var poll_values = new Array();
function poll_set_nr_of_answers()
{
    var nr_of_answers = document.edit_poll.f_nr_of_answers.value;
    var n = 1;
    var m = 1;
    var value = false;

    for (n = 1; n <= nr_of_answers; n++) {
        document.getElementById('poll_answer_tr_' + n).style.display = 'table-row';
        
        if (poll_values[n] && poll_values[n] != '__undefined__') { 
            document.getElementById('poll_answer_input_' + n).value = poll_values[n];    
        } else {
            if (document.getElementById('poll_answer_input_' + n).value == '__undefined__') {
                document.getElementById('poll_answer_input_' + n).value = ''; 
            }
        }
    }
    
    for (m = n; m <= 20; m++) { 
        document.getElementById('poll_answer_tr_' + m).style.display = 'none';
        
        value = document.getElementById('poll_answer_input_' + m).value;
        if (value.length) { 
            poll_values[m] = value;     
        }
        document.getElementById('poll_answer_input_' + m).value = '__undefined__';   
    }
}
</script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>