<?php
$translator = \Zend_Registry::get('container')->getService('translator');

// Check permissions
if (!$g_user->hasPermission('plugin_poll')) {
    camp_html_display_error($translator->trans('You do not have the right to manage polls.', array(), 'plugin_poll'));
    exit;
}

$allLanguages = Language::GetLanguages();

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

if ($f_poll_nr && $f_fk_language_id) {
    $poll = new Poll($f_fk_language_id, $f_poll_nr);
    
    if (Input::Get('submit', 'boolean')) {
        // create the hitlist
        
        
        
            
    } elseif ($poll->exists()) {  
        $poll_nr = $poll->getNumber(); 
        $title = $poll->getProperty('title');
        $question = $poll->getProperty('question');
        $date_begin = $poll->getProperty('date_begin');
        $date_end = $poll->getProperty('date_end');
        $nr_of_answers = $poll->getProperty('nr_of_answers');
        $fk_language_id = $poll->getProperty('fk_language_id');
        $is_display_expired = $poll->getProperty('is_display_expired');
        $is_used_as_default = $poll->getProperty('is_used_as_default');
        
        $poll_answers = $poll->getAnswers();
        foreach ($poll_answers as $poll_answer) {
            $answers[$poll_answer->getProperty('nr_answer')] = $poll_answer->getProperty('answer');   
        }
    }
}
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
    <TR>
        <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
        <TD><A HREF="index.php"><B><?php  echo $translator->trans("Poll List", array(), 'plugin_poll'); ?></B></A></TD>
    </TR>
</TABLE>

<?php
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
camp_html_display_msgs();
?>
<P>
<FORM NAME="edit_poll" METHOD="POST" ACTION="hitlist.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<INPUT TYPE="HIDDEN" NAME="f_poll_nr" VALUE="<?php p($poll->getNumber()); ?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <TD COLSPAN="2">
        <B><?php  if ($poll) echo $translator->trans("Edit Poll", array(), 'plugin_poll'); else echo $translator->trans('Add new Poll', array(), 'plugin_poll'); ?></B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <td valign="top">
        <table>
        <tr>
            <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Title", array(), 'plugin_poll'); ?>:</TD>
            <TD>
            <INPUT TYPE="TEXT" NAME="f_title" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Title', array(), 'plugin_poll'))); ?>" value="<?php echo htmlspecialchars($title); ?>">
            </TD>
        </TR>
        <tr>
            <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Question", array(), 'plugin_poll'); ?>:</TD>
            <TD>
            <TEXTAREA NAME="f_question" class="input_textarea" cols="28" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Question', array(), 'plugin_poll'))); ?>"><?php echo htmlspecialchars($question); ?></TEXTAREA>
            </TD>
        </TR>
        
        <?php
        for ($n=1; $n<=20; $n++) {
            ?>
            <tr id="poll_answer_tr_<?php p($n); ?>" style="display: <?php $nr_of_answers >= $n ? p('table-row') : p('none'); ?>">
                <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Answer $1", array('$1' => $n), 'plugin_poll'); ?>:</TD>
                <TD>
                <INPUT TYPE="TEXT" NAME="f_answer[<?php p($n); ?>]" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" id="poll_answer_input_<?php p($n); ?>" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Answer $1', array('$1' => $n), 'plugin_poll'))); ?>" value="<?php isset($answers[$n]) ? p(htmlspecialchars($answers[$n])) : p('__undefined__'); ?>">
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
<?php
if (!$f_include) {
    camp_html_copyright_notice(); 
}    
?>
