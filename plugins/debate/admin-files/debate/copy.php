<?php
camp_load_translation_strings("plugin_debate");

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error(getGS('You do not have the right to manage debates.'));
    exit;
}

$allLanguages = Language::GetLanguages();

$f_debate_nr = Input::Get('f_debate_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

$debate = new Debate($f_fk_language_id, $f_debate_nr);

if (!$debate->exists()) {
    camp_html_display_error(getGS('Debate does not exists.'));
    exit;
}

$title = $debate->getProperty('title');
$question = $debate->getProperty('question');
$date_begin = $debate->getProperty('date_begin');
$date_end = $debate->getProperty('date_end');
$fk_language_id = $debate->getProperty('fk_language_id');
$votes_per_user = $debate->getProperty('votes_per_user');

/*
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
                  'Section' => $sectionObj);
camp_html_content_top(getGS('Add new article'), $topArray, true, false, array(getGS("Articles") => "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"));
*/
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
    <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
    <TD><A HREF="index.php"><B><?php  putGS("Debate List"); ?></B></A></TD>
</TR>
</TABLE>

<?php
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
camp_html_display_msgs();
?>
<P>
<FORM NAME="duplicate_debate" METHOD="POST" ACTION="do_copy.php" onsubmit="return (<?php camp_html_fvalidate(); ?> && checkForm());">
<?php echo SecurityToken::FormParameter(); ?>
<INPUT TYPE="HIDDEN" NAME="f_debate_nr" VALUE="<?php  p($debate->getNumber()); ?>">
<INPUT TYPE="HIDDEN" NAME="f_fk_language_id" VALUE="<?php  p($debate->getLanguageId()); ?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <TD COLSPAN="2">
        <B><?php  putGS("Duplicate Debate"); ?></B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <td valign="top">
        <table>
          <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Date begin voting"); ?>:</TD>
            <TD>
                <?php $now = getdate(); ?>
                <INPUT TYPE="TEXT" class="input_text date" NAME="f_date_begin" id="f_date_begin" maxlength="10" SIZE="11" VALUE="<?php p($date_begin); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date begin')."'"); ?>" />
            </TD>
        </TR>
        <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Date end voting"); ?>:</TD>
            <TD>
                <?php $now = getdate(); ?>
                <INPUT TYPE="TEXT" class="input_text date" NAME="f_date_end" id="f_date_end" maxlength="10" SIZE="11" VALUE="<?php p($date_end); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date end')."'"); ?>" />
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Title"); ?>:</TD>
            <TD>
            <INPUT TYPE="TEXT" NAME="f_title" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Title')); ?>" value="<?php echo htmlspecialchars($title); ?>">
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Question"); ?>:</TD>
            <TD>
            <TEXTAREA NAME="f_question" class="input_textarea" cols="28" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Question')); ?>"><?php echo htmlspecialchars($question); ?></TEXTAREA>
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Votes per single User"); ?>:</TD>
            <TD style="padding-top: 3px;">
                <SELECT NAME="f_votes_per_user" alt="select" emsg="<?php putGS("You must select number of votes per user.")?>" class="input_select" onchange="debate_set_nr_of_answers()">
                <option value="0"><?php putGS("---Select---"); ?></option>
                <?php
                 for($n=1; $n<=255; $n++) {
                     camp_html_select_option($n,
                                             isset($votes_per_user) ? $votes_per_user : 1,
                                             $n);
                }
                ?>
                </SELECT>
            </TD>
        </TR>
        <?php
        foreach ($debate->getAnswers() as $answer) {
            ?>
            <tr>
                <TD ALIGN="RIGHT" ><?php  putGS("Answer $1", $answer->getNumber()); ?>:</TD>
                <TD>
                    <INPUT TYPE="TEXT" NAME="f_answer[<?php p($answer->getNumber()); ?>][text]" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Answer $1', $answer->getNumber())); ?>" value="<?php p(htmlspecialchars($answer->getProperty('answer'))); ?>">
                </TD>
                <TD>
                    <INPUT TYPE="checkbox" NAME="f_answer[<?php p($answer->getNumber()); ?>][number]" value="<?php p($answer->getNumber()); ?>" class="input_text" >
                </TD>
            </TR>
            <?php
        }
        ?>
        <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Copy statistics"); ?>:</TD>
            <TD>
            <INPUT TYPE="checkbox" NAME="f_copy_statistics" class="input_checkbox" value="1">
            </TD>
        </TR>
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

<script language="javascript">
function checkForm() {
    var checked = false;

    for (var i = 0; i < document.forms['duplicate_debate'].length; i++) {
        if (document.forms['duplicate_debate'].elements[i].name.indexOf('[number]') != -1 &&
            document.forms['duplicate_debate'].elements[i].checked) {

            checked = true;
        }
    }

    if (!checked) {
        alert("<?php putGS('You need to activate at least 1 answer.') ?>");
        return false;
    }
    return true;
}
</script>
