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
$f_from = Input::Get('f_from', 'string', false);

$debate = new Debate($f_fk_language_id, $f_debate_nr);

if ($debate->exists()) {
    // edit existing debate
    $parent_debate_nr = $debate->getProperty('parent_debate_nr');
    $is_extended = $debate->isExtended();
    $title = $debate->getProperty('title');
    $question = $debate->getProperty('question');
    $date_begin = $debate->getProperty('date_begin');
    $date_end = $debate->getProperty('date_end');
    $nr_of_answers = $debate->getProperty('nr_of_answers');
    $fk_language_id = $debate->getProperty('fk_language_id');
    $votes_per_user = $debate->getProperty('votes_per_user');
    $allow_not_logged_in = $debate->getProperty('allow_not_logged_in');
    $results_time_unit = $debate->getProperty('results_time_unit');

    $debate_answers = $debate->getAnswers();

    foreach ($debate_answers as $debate_answer) {
        $answers[$debate_answer->getProperty('nr_answer')] = $debate_answer->getProperty('answer');
    }

} else {
    // language_id may preset from from assign_popup.php
    $fk_language_id = Input::Get('f_language_id', 'int');
}

if (empty($GLOBALS['_popup'])) {
    $pageTitle = $debate->exists() ? getGS('Edit Debate') : getGS('Add new Debate');
    echo camp_html_breadcrumbs(array(
        array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
        array(getGS('Debates'), $Campsite['WEBSITE_URL'] . '/admin/debate/index.php'),
        array($pageTitle, ''),
    ));
}

if (!isset($f_include) || !$f_include) : ?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
        <tr>
            <td><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></td>
            <td><A HREF="index.php"><B><?php  putGS("Debate List"); ?></B></A></td>
        </tr>
    </TABLE>
<?php else : ?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
        <tr>
            <td><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></td>
            <td><A HREF="<?php p(urldecode($f_from)) ?>"><B><?php  putGS("Attach Debate"); ?></B></A></td>
        </tr>
    </TABLE>
<?php
endif;

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
camp_html_display_msgs();
?>

<P>
<form name="edit_debate" id="edit-debate-form" method="POST" action="do_edit.php">
<?php echo SecurityToken::FormParameter(); ?>

<?php if ($debate->exists()) : ?>
	<INPUT TYPE="HIDDEN" NAME="f_debate_nr" VALUE="<?php p($debate->getNumber()); ?>">
<?php endif; ?>

<?php if ($f_from) : ?>
	<INPUT TYPE="HIDDEN" NAME="f_from" VALUE="<?php p(htmlspecialchars($f_from)); ?>">
<?php endif; ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<tr>
    <td valign="top">
        <table>
          <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</td>
            <td style="padding-top: 3px;">
                <?php if (count($allLanguages) > 1) : ?>
                <SELECT NAME="f_fk_language_id" alt="select" emsg="<?php putGS("You must select a language.")?>" class="input_select">
                <option value="0"><?php putGS("---Select language---"); ?></option>
                <?php
                foreach ($allLanguages as $tmpLanguage) {
                     camp_html_select_option($tmpLanguage->getLanguageId(), $fk_language_id, $tmpLanguage->getNativeName());
                }
                ?>
                </SELECT>
                <?php else :
                    $tmpLanguage = array_pop($allLanguages);
                    echo '<b>'.htmlspecialchars($tmpLanguage->getNativeName()).'</b>';
                ?>
                    <input type="hidden" name="f_article_language" value="<?php p($tmpLanguage->getLanguageId()); ?>">
                <?php endif; ?>

            </td>
          </tr>
          <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</td>
            <td>
                <SELECT NAME="f_is_extended" class="input_select">
                <?php if ($debate->getProperty('parent_debate_nr')) : ?>
                    <option value="0"><?php putGS('Copy') ?></option>
                <?php else : ?>
                    <option value="0"><?php putGS('Standard') ?></option>
                    <option value="1" <?php isset($is_extended) && $is_extended ? p('selected="selected"') : null ?>><?php putGS('Extended') ?></option>
                <?php endif; ?>
                </SELECT>
            </td>
          </tr>
          <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Date begin voting"); ?>:</td>
            <td>
                <?php $now = getdate(); ?>
                <INPUT TYPE="TEXT" class="input_text date" NAME="f_date_begin" id="f_date_begin" maxlength="10" SIZE="11"
                	VALUE="<?php if (isset($date_begin)) p($date_begin); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date begin')."'"); ?>" />
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Date end voting"); ?>:</td>
            <td>
                <?php $now = getdate(); ?>
                <INPUT TYPE="TEXT" class="input_text date" NAME="f_date_end" id="f_date_end" maxlength="10" SIZE="11"
                	VALUE="<?php if (isset($date_end)) p($date_end); ?>" alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Date end')."'"); ?>" />
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Title"); ?>:</td>
            <td>
            <INPUT TYPE="TEXT" NAME="f_title" id="input-title" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Title')); ?>" value="<?php if (isset($title)) echo htmlspecialchars($title); ?>">
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Question"); ?>:</td>
            <td>
            <TEXTAREA NAME="f_question" class="input_textarea" cols="28" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Question')); ?>"><?php if (isset($question)) echo htmlspecialchars($question); ?></TEXTAREA>
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  putGS("Votes per unique User"); ?>:</td>
            <td style="padding-top: 3px;">
                <SELECT NAME="f_votes_per_user" alt="select" emsg="<?php putGS("You must select number of votes per user.")?>" class="input_select">
                <option value="0"><?php putGS("---Select---"); ?></option>
                <?php
                    for($n=1; $n<=255; $n++) {
                        camp_html_select_option($n, isset($votes_per_user) ? $votes_per_user : 1, $n);
                    }
                ?>
                </SELECT>
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php putGS("Allow not logged in users") ?>:</td>
            <td style="padding-top: 3px;">
            	<select name="f_allow_not_logged_in" class="input_select">
	                <option value="0" <?php if (isset($allow_not_logged_in) && !$allow_not_logged_in) : ?>selected="selected"<?php endif ?>><?php putGS("No") ?></option>
	                <option value="1" <?php if (isset($allow_not_logged_in) && $allow_not_logged_in) : ?>selected="selected"<?php endif ?>><?php putGS("Yes") ?></option>
                </select>
            </td>
        </tr>

        <tr>
            <td ALIGN="RIGHT" ><?php putGS("Results") ?>:</td>
            <td style="padding-top: 3px;">
            	<select name="f_results_time_unit" class="input_select">
            		<?php foreach ( array( getGS('Daily'), getGS('Weekly'), getGS('Monthly') ) as $tunit ) : ?>
            		<option value="<?php echo ($ltunit = strtolower($tunit)) ?>"
            			<?php if (isset($results_time_unit) && $tunit == $results_time_unit) : ?>selected="selected"<?php endif ?> >
            			<?php echo $tunit ?>
            		</option>
            		<?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
        </tr>

        <?php if (!$debate->getProperty('parent_debate_nr')) : ?>
            <tr>
                <td ALIGN="RIGHT" ><?php  putGS("Number of answers"); ?>:</td>
                <td style="padding-top: 3px;">
                    <SELECT NAME="f_nr_of_answers" id="input-nr-answers" alt="select" emsg="<?php putGS("You must select number of answers.")?>" class="input_select"> <!-- onchange="debate_set_nr_of_answers()"-->
                    <option value="0"><?php putGS("---Select---"); ?></option>
                    <?php
                        for($n=2; $n<=255; $n++) {
                            camp_html_select_option($n, isset($nr_of_answers) ? $nr_of_answers : null, $n);
                        }
                    ?>
                    </SELECT>
                </td>
            </tr>
        <?php endif; ?>

    		<tr id="answer-row" class="answer-row" style="display:none">
            	<td align="right"><?php putGS("Answer %s"); ?>:</td>
                <td>
                	<input type="text" name="f_answer[%s]" id="answer-tpl-input" size="40" maxlength="255" class="input_text" alt="blank"
                		emsg-tpl="<?php putGS('You must fill in the $1 field %s.', getGS('Answer')); ?>" value="" disabled="disabled"/>
    			</td>
    			<td align='center'>
    				<a stlye="display:none" href="javascript: void(0);" onclick="window.open('files/popup.php?f_debate_nr=<?php p($debate->getNumber()); ?>&amp;f_debateanswer_nr=<?php p($n) ?>&amp;f_fk_language_id=<?php p($debate->getLanguageId()); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=600, top=200, left=100');">
                    	<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" border="0">
                	</a>
    			</td>
            </tr>

        </table>
    </td>
</tr>
<tr>
    <td COLSPAN="2" align="center">
        <HR NOSHADE SIZE="1" COLOR="BLACK">
        <INPUT TYPE="submit" NAME="save" VALUE="<?php  putGS('Save'); ?>" class="button">
    </td>
</tr>
</TABLE>
</FORM>

<P>
<script>

$('#edit-debate-form #input-title').focus();
$('#answer-row').data('answers', <?php echo isset($answers) ? json_encode(array_map( 'htmlspecialchars', array_values($answers))) : "[]" ?>);
$('#input-nr-answers').change( function()
{
	$('#answer-row').nextAll('.answer-row').remove();
	var nrAnswers = parseInt($('#input-nr-answers').val());
	for (n=0; n<nrAnswers; n++)
	{
		var newAnswer = $('#answer-row').clone();
		newAnswer.removeAttr('id');
		var newTd = newAnswer.find('td:eq(0)');
		newTd.text(newTd.text().replace('%s', nrAnswers-n));
		var newInput = newAnswer.find('input');
		newInput.attr('name', newInput.attr('name').replace('%s', nrAnswers-n));
		newInput.attr('emsg', newInput.attr('emsg-tpl').replace('%s', nrAnswers-n))
			.val( $('#answer-row').data('answers')[nrAnswers-n-1] )
			.removeAttr('disabled')
			.removeAttr('id');
		newAnswer.insertAfter($('#answer-row')).show();
	}
})
.val($('#answer-row').data('answers').length)
.change();

$('.answer-row input[type=text]').live('blur', function()
{
	var idx = $('#answer-row').nextAll('.answer-row').index($(this).parents('tr:eq(0)'));
	$('#answer-row').data('answers')[idx] = $(this).val();
});
$('#edit-debate-form').submit( function()
{
	if ($('.answer-row').length < 2)
	{
		alert('<?php putGS('Please input at least 2 answers')?>');
		return false;
	}
	$(this).find('#answer-tpl-input').remove();
	return <?php camp_html_fvalidate(); ?>;
})
</script>
<?php
if (!isset($f_include) || !$f_include) {
    camp_html_copyright_notice();
}
?>
