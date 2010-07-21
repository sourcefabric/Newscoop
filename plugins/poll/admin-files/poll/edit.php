<?php
camp_load_translation_strings("plugin_poll");

// Check permissions
if (!$g_user->hasPermission('plugin_poll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$allLanguages = Language::GetLanguages();

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');
$f_from = Input::Get('f_from', 'string', false);

$poll = new Poll($f_fk_language_id, $f_poll_nr);

if ($poll->exists()) {
    // edit existing poll
    $parent_poll_nr = $poll->getProperty('parent_poll_nr');
    $is_extended = $poll->isExtended();
    $title = $poll->getProperty('title');
    $question = $poll->getProperty('question');
    $date_begin = $poll->getProperty('date_begin');
    $date_end = $poll->getProperty('date_end');
    $nr_of_answers = $poll->getProperty('nr_of_answers');
    $fk_language_id = $poll->getProperty('fk_language_id');
    $votes_per_user = $poll->getProperty('votes_per_user');

    $poll_answers = $poll->getAnswers();

    foreach ($poll_answers as $poll_answer) {
        $answers[$poll_answer->getProperty('nr_answer')] = $poll_answer->getProperty('answer');
    }

} else {
    // language_id may preset from from assign_popup.php
    $fk_language_id = Input::Get('f_language_id', 'int');
}

if (!$f_include) { ?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
        <TR>
            <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
            <TD><A HREF="index.php"><B><?php  putGS("Poll List"); ?></B></A></TD>
        </TR>
    </TABLE>
<?php
} else {
?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
        <TR>
            <TD><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
            <TD><A HREF="<?php p(urldecode($f_from)) ?>"><B><?php  putGS("Attach Polls"); ?></B></A></TD>
        </TR>
    </TABLE>
<?php
}


include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
camp_html_display_msgs();
?>
<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-system.css);</style>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/lang/calendar-<?php echo camp_session_get('TOL_Language', 'en'); ?>.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/jscalendar/calendar-setup.js"></script>

<P>
<FORM NAME="edit_poll" METHOD="POST" ACTION="do_edit.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<?php if ($poll->exists()) { ?>
<INPUT TYPE="HIDDEN" NAME="f_poll_nr" VALUE="<?php p($poll->getNumber()); ?>">
<?php } ?>
<?php if ($f_from) { ?>
<INPUT TYPE="HIDDEN" NAME="f_from" VALUE="<?php p(htmlspecialchars($f_from)); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <TD COLSPAN="2">
        <B><?php  if ($poll->exists()) putGS("Edit Poll"); else putGS('Add new Poll'); ?></B>
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
          <tr>
            <TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
            <TD>
                <SELECT NAME="f_is_extended" class="input_select">
                <?php if ($poll->getProperty('parent_poll_nr')) { ?>
                    <option value="0"><?php putGS('Copy') ?></option>
                <?php } else { ?>
                    <option value="0"><?php putGS('Standard') ?></option>
                    <option value="1" <?php $is_extended ? p('selected="selected"') : null ?>><?php putGS('Extended') ?></option>
                <?php } ?>
                </SELECT>
            </TD>
          </TR>
          <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Date begin voting"); ?>:</TD>
            <TD>
                <?php $now = getdate(); ?>
                <table cellpadding="0" cellspacing="2"><tr>
                    <td><INPUT TYPE="TEXT" class="input_text" NAME="f_date_begin" id="f_date_begin" maxlength="10" SIZE="11" VALUE="<?php p($date_begin); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date begin')."'"); ?>"></td>
                    <td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_date_begin"
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
                        button:"f_trigger_date_begin"
                    });
                </script>

            </TD>
        </TR>
        <TR>
            <TD ALIGN="RIGHT" ><?php  putGS("Date end voting"); ?>:</TD>
            <TD>
                <?php $now = getdate(); ?>
                <table cellpadding="0" cellspacing="2"><tr>
                    <td><INPUT TYPE="TEXT" class="input_text" NAME="f_date_end" id="f_date_end" maxlength="10" SIZE="11" VALUE="<?php p($date_end); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date end')."'"); ?>"></td>
                    <td valign="top" align="left"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/calendar.gif" id="f_trigger_date_end"
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
                        button:"f_trigger_date_end"
                    });
                </script>

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
            <TD ALIGN="RIGHT" ><?php  putGS("Votes per unique User"); ?>:</TD>
            <TD style="padding-top: 3px;">
                <SELECT NAME="f_votes_per_user" alt="select" emsg="<?php putGS("You must select number of votes per user.")?>" class="input_select">
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

        <?php if (!$poll->getProperty('parent_poll_nr')) { ?>
            <TR>
                <TD ALIGN="RIGHT" ><?php  putGS("Number of answers"); ?>:</TD>
                <TD style="padding-top: 3px;">
                    <SELECT NAME="f_nr_of_answers" alt="select" emsg="<?php putGS("You must select number of answers.")?>" class="input_select" onchange="poll_set_nr_of_answers()">
                    <option value="0"><?php putGS("---Select---"); ?></option>
                    <?php
                     for($n=1; $n<=255; $n++) {
                         camp_html_select_option($n,
                                                 $nr_of_answers,
                                                 $n);
                    }
                    ?>
                    </SELECT>
                </TD>
            </TR>
        <?php } ?>

        <?php
        for ($n=1; $n<=255; $n++) {
            ?>
            <tr id="poll_answer_tr_<?php p($n); ?>" style="display: <?php is_array($answers) && array_key_exists($n, $answers) ? p('table-row') : p('none'); ?>">
                <TD ALIGN="RIGHT" ><?php  putGS("Answer $1", $n); ?>:</TD>
                <TD>
                    <INPUT TYPE="TEXT" NAME="f_answer[<?php p($n); ?>]" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" id="poll_answer_input_<?php p($n); ?>" emsg="<?php putGS('You must fill in the $1 field.', getGS('Answer $1', $n)); ?>" value="<?php isset($answers[$n]) ? p(htmlspecialchars($answers[$n])) : p('__undefined__'); ?>">
                </TD>

                <?php if ($poll->exists()) { ?>
                    <td align='center'>
                        <a href="javascript: void(0);" onclick="window.open('files/popup.php?f_poll_nr=<?php p($poll->getNumber()); ?>&amp;f_pollanswer_nr=<?php p($n) ?>&amp;f_fk_language_id=<?php p($poll->getLanguageId()); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=600, top=200, left=100');">
                            <IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" BORDER="0">
                        </a>
                    </td>
                <?php } ?>
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
        document.getElementById('poll_answer_tr_' + n).style.display = '';

        if (poll_values[n] && poll_values[n] != '__undefined__') {
            document.getElementById('poll_answer_input_' + n).value = poll_values[n];
        } else {
            if (document.getElementById('poll_answer_input_' + n).value == '__undefined__') {
                document.getElementById('poll_answer_input_' + n).value = '';
            }
        }
    }

    for (m = n; m <= 200; m++) {
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