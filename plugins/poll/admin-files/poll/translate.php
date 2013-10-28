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

$poll = new Poll($f_fk_language_id, $f_poll_nr);

if ($poll->exists()) {
    foreach ($poll->getTranslations() as $translation) {
        $existing[$translation->getLanguageId()] = true;
    }
    $title = $poll->getProperty('title');
    $question = $poll->getProperty('question');
    $is_used_as_default = false;
}

echo camp_html_breadcrumbs(array(
    array($translator->trans('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array($translator->trans('Polls', array(), 'plugin_poll'), $Campsite['WEBSITE_URL'] . '/admin/poll/index.php'),
    array($translator->trans('Translate Poll', array(), 'plugin_poll'), ''),
));
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
<FORM NAME="edit_poll" METHOD="POST" ACTION="do_translate.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<?php if ($poll) { ?>
<INPUT TYPE="HIDDEN" NAME="f_poll_nr" VALUE="<?php  p($poll->getNumber()); ?>">
<INPUT TYPE="HIDDEN" NAME="f_fk_language_id" VALUE="<?php  p($poll->getLanguageId()); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <td valign="top">
        <table>
          <TR>
            <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Language"); ?>:</TD>
            <TD style="padding-top: 3px;">
                <?php if (count($allLanguages) > 1) { ?>
                <SELECT NAME="f_target_language_id" alt="select" emsg="<?php echo $translator->trans("You must select a language.")?>" class="input_select">
                <option value="0"><?php echo $translator->trans("---Select language---"); ?></option>
                <?php
                 foreach ($allLanguages as $tmpLanguage) {
                   if (!array_key_exists($tmpLanguage->getLanguageId(), $existing)) {
                     camp_html_select_option($tmpLanguage->getLanguageId(),
                                             $f_target_language_id,
                                             $tmpLanguage->getNativeName());
                   }
                }
                ?>
                </SELECT>
                <?php } else {
                    $tmpLanguage = array_pop($allLanguages);
                    echo '<b>'.htmlspecialchars($tmpLanguage->getNativeName()).'</b>';
                    ?>
                    <input type="hidden" name="f_target_language_id" value="<?php p($tmpLanguage->getLanguageId()); ?>">
                    <?php
                }
                ?>

            </TD>
        </TR>
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
        foreach ($poll->getAnswers() as $answer) {
            ?>
            <tr>
                <TD ALIGN="RIGHT" ><?php  echo $translator->trans("Answer $1", array('$1' => $answer->getNumber()), 'plugin_poll'); ?>:</TD>
                <TD>
                <INPUT TYPE="TEXT" NAME="f_answer[<?php p($answer->getNumber()); ?>]" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Answer $1', array('$1' => $answer->getNumber()), 'plugin_poll'))); ?>" value="<?php p(htmlspecialchars($answer->getProperty('answer'))); ?>">
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
        <INPUT TYPE="submit" NAME="save" VALUE="<?php  echo $translator->trans('Save'); ?>" class="button">
    </TD>
</TR>
</TABLE>
</FORM>
