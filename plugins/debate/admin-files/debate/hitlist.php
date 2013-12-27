<?php
$translator = \Zend_Registry::get('container')->getService('translator');

// Check permissions
if (!$g_user->hasPermission('plugin_debate_admin')) {
    camp_html_display_error($translator->trans('You do not have the right to manage debates.', array(), 'plugin_debate'));
    exit;
}

$allLanguages = Language::GetLanguages();

$f_debate_nr = Input::Get('f_debate_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

if ($f_debate_nr && $f_fk_language_id) {
    $debate = new Debate($f_fk_language_id, $f_debate_nr);

    if (Input::Get('submit', 'boolean')) {
        // create the hitlist




    } elseif ($debate->exists()) {
        $debate_nr = $debate->getNumber();
        $title = $debate->getProperty('title');
        $question = $debate->getProperty('question');
        $date_begin = $debate->getProperty('date_begin');
        $date_end = $debate->getProperty('date_end');
        $nr_of_answers = $debate->getProperty('nr_of_answers');
        $fk_language_id = $debate->getProperty('fk_language_id');
        $is_display_expired = $debate->getProperty('is_display_expired');
        $is_used_as_default = $debate->getProperty('is_used_as_default');

        $debate_answers = $debate->getAnswers();
        foreach ($debate_answers as $debate_answer) {
            $answers[$debate_answer->getProperty('nr_answer')] = $debate_answer->getProperty('answer');
        }
    }
}
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
    <TR>
        <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
        <TD><A HREF="index.php"><B><?php  echo $translator->trans("Debate List", array(), 'plugin_debate'); ?></B></A></TD>
    </TR>
</TABLE>

<?php
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
camp_html_display_msgs();
?>
<P>
<FORM NAME="edit_debate" METHOD="POST" ACTION="hitlist.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<INPUT TYPE="HIDDEN" NAME="f_debate_nr" VALUE="<?php p($debate->getNumber()); ?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <TD COLSPAN="2">
        <B><?php  if ($debate) echo $translator->trans("Edit Debate", array(), 'plugin_debate'); else echo $translator->trans('Add new Debate', array(), 'plugin_debate'); ?></B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <td valign="top">
        <table>
        <tr>
            <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Title", array(), 'plugin_debate'); ?>:</TD>
            <TD>
            <INPUT TYPE="TEXT" NAME="f_title" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Title', array(), 'plugin_debate'))); ?>" value="<?php echo htmlspecialchars($title); ?>">
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Question", array(), 'plugin_debate'); ?>:</TD>
            <TD>
            <TEXTAREA NAME="f_question" class="input_textarea" cols="28" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Question', array(), 'plugin_debate'))); ?>"><?php echo htmlspecialchars($question); ?></TEXTAREA>
            </TD>
        </TR>

        <?php
        for ($n=1; $n<=20; $n++) {
            ?>
            <tr id="debate_answer_tr_<?php p($n); ?>" style="display: <?php $nr_of_answers >= $n ? p('table-row') : p('none'); ?>">
                <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Answer $1", array('$1' => $n), 'plugin_debate'); ?>:</TD>
                <TD>
                <INPUT TYPE="TEXT" NAME="f_answer[<?php p($n); ?>]" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" id="debate_answer_input_<?php p($n); ?>" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Answer $1', array('$1' => $n), 'plugin_debate'))); ?>" value="<?php isset($answers[$n]) ? p(htmlspecialchars($answers[$n])) : p('__undefined__'); ?>">
                </TD>

                <td align='center'>
                    <INPUT type="checkbox" name="f_answer[<?php p($n); ?>]">
                </td>

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
        <INPUT TYPE="submit" NAME="save" VALUE="<?php  echo $translator->trans('Save'); ?>" class="button">
    </TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.edit_debate.f_title.focus();

var debate_values = new Array();
function debate_set_nr_of_answers()
{
    var nr_of_answers = document.edit_debate.f_nr_of_answers.value;
    var n = 1;
    var m = 1;
    var value = false;

    for (n = 1; n <= nr_of_answers; n++) {
        document.getElementById('debate_answer_tr_' + n).style.display = 'table-row';

        if (debate_values[n] && debate_values[n] != '__undefined__') {
            document.getElementById('debate_answer_input_' + n).value = debate_values[n];
        } else {
            if (document.getElementById('debate_answer_input_' + n).value == '__undefined__') {
                document.getElementById('debate_answer_input_' + n).value = '';
            }
        }
    }

    for (m = n; m <= 20; m++) {
        document.getElementById('debate_answer_tr_' + m).style.display = 'none';

        value = document.getElementById('debate_answer_input_' + m).value;
        if (value.length) {
            debate_values[m] = value;
        }
        document.getElementById('debate_answer_input_' + m).value = '__undefined__';
    }
}
</script>
<?php
if (!$f_include) {
    camp_html_copyright_notice();
}
?>
