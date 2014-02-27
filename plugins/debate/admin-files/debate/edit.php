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
    $time_begin = strftime('%H:%M', strtotime($date_begin));
    $time_end = strftime('%H:%M', strtotime($date_end));
    $date_begin = strftime('%Y-%m-%d', strtotime($date_begin));
    $date_end = strftime('%Y-%m-%d', strtotime($date_end));

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
    $pageTitle = $debate->exists() ? $translator->trans('Edit Debate', array(), 'plugin_debate') : $translator->trans('Add new Debate', array(), 'plugin_debate');
    echo camp_html_breadcrumbs(array(
        array($translator->trans('Plugins', array(), 'plugin_debate'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
        array($translator->trans('Debates', array(), 'plugin_debate'), $Campsite['WEBSITE_URL'] . '/admin/debate/index.php'),
        array($pageTitle, ''),
    ));
}

if (!isset($f_include) || !$f_include) : ?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
        <tr>
            <td><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></td>
            <td><A HREF="index.php"><B><?php  echo $translator->trans("Debate List", array(), 'plugin_debate'); ?></B></A></td>
        </tr>
    </TABLE>
<?php else : ?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
        <tr>
            <td><A HREF="index.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></td>
            <td><A HREF="<?php p(urldecode($f_from)) ?>"><B><?php  echo $translator->trans("Attach Debate", array(), 'plugin_debate'); ?></B></A></td>
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
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Language"); ?>:</td>
            <td style="padding-top: 3px;">
                <?php if (count($allLanguages) > 1) : ?>
                <SELECT NAME="f_fk_language_id" alt="select" emsg="<?php echo $translator->trans("You must select a language.")?>" class="input_select">
                <option value="0"><?php echo $translator->trans("---Select language---"); ?></option>
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
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Type"); ?>:</td>
            <td>
                <SELECT NAME="f_is_extended" class="input_select">
                <?php if ($debate->getProperty('parent_debate_nr')) : ?>
                    <option value="0"><?php echo $translator->trans('Copy', array(), 'plugin_debate') ?></option>
                <?php else : ?>
                    <option value="0"><?php echo $translator->trans('Standard', array(), 'plugin_debate') ?></option>
                    <option value="1" <?php isset($is_extended) && $is_extended ? p('selected="selected"') : null ?>><?php echo $translator->trans('Extended', array(), 'plugin_debate') ?></option>
                <?php endif; ?>
                </SELECT>
            </td>
          </tr>
          <tr>
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Date begin voting", array(), 'plugin_debate'); ?>:</td>
            <td>
                <?php $now = getdate(); ?>

                <input type="text" class="input_text date" NAME="f_date_begin" id="f_date_begin" maxlength="10" SIZE="11"
                	value="<?php if (isset($date_begin)) p($date_begin); else p(strftime('%Y-%m-%d', strtotime("Friday"))); ?>"
                	alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Date begin', array(), 'plugin_debate'))); ?>" />

               	<input type="text" class="input_text time" name="f_time_begin" id="f_time_begin" maxlength="5" size="5"
               		value="<?php if (isset($time_begin)) p($time_begin); else p("12:00"); ?>" />
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Date end voting", array(), 'plugin_debate'); ?>:</td>
            <td>
                <?php $now = getdate(); ?>

                <input type="text" class="input_text date" NAME="f_date_end" id="f_date_end" maxlength="10" SIZE="11"
                	value="<?php if (isset($date_end)) p($date_end); else p(strftime('%Y-%m-%d', strtotime("Thursday + 1 week"))); ?>"
                	alt="date|yyyy/mm/dd|-|0|<?php echo $now["year"]."/".$now["mon"]."/".$now["mday"]; ?>" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => "'".$translator->trans('Date end', array(), 'plugin_debate')."'")); ?>" />

                <input type="text" class="input_text time" name="f_time_end" id="f_time_end" maxlength="5" size="5"
                	value="<?php if (isset($time_end)) p($time_end); else p("11:59"); ?>" />
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Title", array(), 'plugin_debate'); ?>:</td>
            <td>
            <input type="text" NAME="f_title" id="input-title" SIZE="40" MAXLENGTH="255" class="input_text" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Title', array(), 'plugin_debate'))); ?>" value="<?php if (isset($title)) echo htmlspecialchars($title); ?>">
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Question", array(), 'plugin_debate'); ?>:</td>
            <td>
            <TEXTAREA NAME="f_question" class="input_textarea" cols="28" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Question', array(), 'plugin_debate'))); ?>"><?php if (isset($question)) echo htmlspecialchars($question); ?></TEXTAREA>
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php  echo $translator->trans("Votes per unique User", array(), 'plugin_debate'); ?>:</td>
            <td style="padding-top: 3px;">
                <SELECT NAME="f_votes_per_user" alt="select" emsg="<?php echo $translator->trans("You must select number of votes per user.", array(), 'plugin_debate')?>" class="input_select">
                <option value="0"><?php echo $translator->trans("---Select---", array(), 'plugin_debate'); ?></option>
                <?php
                    for($n=1; $n<=255; $n++) {
                        camp_html_select_option($n, isset($votes_per_user) ? $votes_per_user : 1, $n);
                    }
                ?>
                </SELECT>
            </td>
        </tr>
        <tr>
            <td ALIGN="RIGHT" ><?php echo $translator->trans("Allow not logged in users", array(), 'plugin_debate') ?>:</td>
            <td style="padding-top: 3px;">
            	<select name="f_allow_not_logged_in" class="input_select">
	                <option value="0" <?php if (isset($allow_not_logged_in) && !$allow_not_logged_in) : ?>selected="selected"<?php endif ?>><?php echo $translator->trans("No") ?></option>
	                <option value="1" <?php if (isset($allow_not_logged_in) && $allow_not_logged_in) : ?>selected="selected"<?php endif ?>><?php echo $translator->trans("Yes") ?></option>
                </select>
            </td>
        </tr>

        <tr>
            <td ALIGN="RIGHT" ><?php echo $translator->trans("Results", array(), 'plugin_debate') ?>:</td>
            <td style="padding-top: 3px;">
            	<select name="f_results_time_unit" class="input_select">
            		<?php foreach ( array( $translator->trans('Daily', array(), 'plugin_debate'), $translator->trans('Weekly', array(), 'plugin_debate'), $translator->trans('Monthly', array(), 'plugin_debate') ) as $tunit ) : ?>
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
                <td ALIGN="RIGHT" ><?php  echo $translator->trans("Number of answers", array(), 'plugin_debate'); ?>:</td>
                <td style="padding-top: 3px;">
                    <SELECT NAME="f_nr_of_answers" id="input-nr-answers" alt="select" emsg="<?php echo $translator->trans("You must select number of answers.", array(), 'plugin_debate')?>" class="input_select"> <!-- onchange="debate_set_nr_of_answers()"-->
                    <option value="0"><?php echo $translator->trans("---Select---", array(), 'plugin_debate'); ?></option>
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
            	<td align="right"><?php echo $translator->trans("Answer %s", array(), 'plugin_debate'); ?>:</td>
                <td>
                	<input type="text" name="f_answer[%s]" id="answer-tpl-input" size="40" maxlength="255" class="input_text" alt="blank"
                		emsg-tpl="<?php echo $translator->trans('You must fill in the $1 field %s.', array('$1' => $translator->trans('Answer', array(), 'plugin_debate'))); ?>" value="" disabled="disabled"/>
    			</td>
    			<?php if ($debate->exists()) : ?>
    			<td align='center'>
    				<a stlye="display:none" href="javascript: void(0);" onclick="window.open('files/popup.php?f_debate_nr=<?php p($debate->getNumber()); ?>&amp;f_debateanswer_nr=<?php p($n) ?>&amp;f_fk_language_id=<?php p($debate->getLanguageId()); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=600, top=200, left=100');">
                    	<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/save.png" border="0">
                	</a>
    			</td>
    			<?php endif; ?>
            </tr>

        </table>
    </td>
</tr>
<tr>
    <td COLSPAN="2" align="center">
        <HR NOSHADE SIZE="1" COLOR="BLACK">
        <INPUT TYPE="submit" NAME="save" VALUE="<?php  echo $translator->trans('Save'); ?>" class="button">
    </td>
</tr>
</TABLE>
</FORM>

<P>
<script>
function prepareDate(oldFormat)
{
    var dates = oldFormat.split('-');
    var returnVal = dates[1] + '/' + dates[2] + '/' + dates[0];
    return returnVal;
}

function timeOk(startDate, startTime, endDate, endTime)
{
    startDate = prepareDate(startDate);
    endDate = prepareDate(endDate);
    var startFull = new Date(startDate + ' ' + startTime);
    var endFull = new Date(endDate + ' ' + endTime);
    if (endFull - startFull >= 0) {
        return true;
    } else {
        return false;
    }
}

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
    var startDate = $('#f_date_begin').val();
    var startTime = $('#f_time_begin').val();
    var endDate = $('#f_date_end').val();
    var endTime = $('#f_time_end').val();
    if (!timeOk(startDate, startTime, endDate, endTime)) {
        valid = 0;
        alert("<?php echo $translator->trans('End time cannot be set before start time', array(), 'plugin_debate'); ?>");
        $('#f_date_end').focus();
        return false;
    }

    if ($('.answer-row').length < 2) {
        alert('<?php echo $translator->trans('Please input at least 2 answers', array(), 'plugin_debate')?>');
        return false;
    }
    $(this).find('#answer-tpl-input').remove();

    return <?php camp_html_fvalidate(); ?>;
})

$(function()
{
	$('input.time#f_time_begin').data('origval', '12:00');
	$('input.time#f_time_end').data('origval', '11:59');
	$('input.time')
	.focus(function()
	{
		var val = $(this).val()
		if( val != '' )
			$(this).data('origval', val );
	})
	.blur(function()
	{
		var val = $(this).val();
		if( val.search(/\d{2}:\d{2}/) ) $(this).trigger('invalid');
		var hrs = val.split(":");
		var hr = parseInt(hrs[0]);
		var min = parseInt(hrs[1]);
		if( hr > 24 || hr < 0 ) $(this).trigger('invalid');
		if( min > 60 || min < 0 ) $(this).trigger('invalid');
	})
	.bind( 'invalid', function()
	{
		$(this).val( $(this).data('origval') );
	});

	<?php if (!$debate->exists()) : ?>
		$('#input-nr-answers').val(2).trigger('change');
	<?php endif; ?>
})
</script>
<?php
if (!isset($f_include) || !$f_include) {
    camp_html_copyright_notice();
}
?>
