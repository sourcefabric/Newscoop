<?php
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

$translator = \Zend_Registry::get('container')->getService('translator');
$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$editMode = ($f_language_id != 0);

if (!$g_user->hasPermission('ManageLanguages')) {
    if (!$editMode) {
	   camp_html_display_error($translator->trans("You do not have the right to add languages.", array(), 'languages'));
    }
    else {
       camp_html_display_error($translator->trans("You do not have the right to edit languages.", array(), 'languages'));
    }
	exit;
}

$q_defaultTimeUnits = $g_ado_db->GetAll("SELECT * FROM TimeUnits WHERE IdLanguage=1");
$numTimeUnits = 0;
$q_timeUnits = array();
if ($editMode) {
    $q_timeUnits = $g_ado_db->GetAll("SELECT * FROM TimeUnits WHERE IdLanguage=$f_language_id");
}

$languageObj = new Language($f_language_id);

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Languages"), "/$ADMIN/languages");
if ($editMode) {
    $crumbs[] = array($translator->trans("Edit language", array(), 'languages'), "");
} else {
    $crumbs[] = array($translator->trans("Add new language", array(), 'languages'), "");
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();
?>
<P>
<FORM NAME="language_form" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/languages/do_add_modify.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<?php if ($editMode) { ?>
<input type="hidden" name="f_language_id" value="<?php p($languageObj->getLanguageId()); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
	   <?php if ($editMode) { ?>
	       <B><?php echo $translator->trans("Edit language", array(), 'languages'); ?></B>
	   <?php } else { ?>
           <B><?php echo $translator->trans("Add new language", array(), 'languages'); ?></B>
       <?php } ?>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_language_name" SIZE="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Name'))); ?>" value="<?php p($languageObj->getProperty('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Native name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_native_name" SIZE="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Native name'))); ?>" value="<?php p($languageObj->getProperty('OrigName')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Code"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_language_code" SIZE="20" MAXLENGTH="20" alt="length|2|20" emsg="<?php  echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Code'))); ?>" value="<?php p($languageObj->getProperty('Code')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  echo $translator->trans('Please enter the translation for month names.', array(), 'languages'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("January", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_1" SIZE="20" value="<?php p($languageObj->getProperty('Month1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("February", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_2" SIZE="20" value="<?php p($languageObj->getProperty('Month2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("March", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_3" SIZE="20" value="<?php p($languageObj->getProperty('Month3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("April", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_4" SIZE="20" value="<?php p($languageObj->getProperty('Month4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("May", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_5" SIZE="20" value="<?php p($languageObj->getProperty('Month5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("June", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_6" SIZE="20" value="<?php p($languageObj->getProperty('Month6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("July", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_7" SIZE="20" value="<?php p($languageObj->getProperty('Month7')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("August", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_8" SIZE="20" value="<?php p($languageObj->getProperty('Month8')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("September", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_9" SIZE="20" value="<?php p($languageObj->getProperty('Month9')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("October", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_10" SIZE="20" value="<?php p($languageObj->getProperty('Month10')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("November", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_11" SIZE="20" value="<?php p($languageObj->getProperty('Month11')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("December", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_12" SIZE="20" value="<?php p($languageObj->getProperty('Month12')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  echo $translator->trans('Please enter the translation for month short names.', array(), 'languages'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Jan", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_1" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Feb", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_2" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Mar", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_3" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Apr", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_4" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("May", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_5" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Jun", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_6" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Jul", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_7" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth7')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Aug", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_8" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth8')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Sep", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_9" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth9')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Oct", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_10" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth10')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Nov", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_11" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth11')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Dec", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_month_12" SIZE="20" value="<?php p($languageObj->getProperty('ShortMonth12')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  echo $translator->trans('Please enter the translation for week day names.', array(), 'languages'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Sunday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_sunday" SIZE="20" value="<?php p($languageObj->getProperty('WDay1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Monday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_monday" SIZE="20" value="<?php p($languageObj->getProperty('WDay2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Tuesday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_tuesday" SIZE="20" value="<?php p($languageObj->getProperty('WDay3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Wednesday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_wednesday" SIZE="20" value="<?php p($languageObj->getProperty('WDay4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Thursday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_thursday" SIZE="20" value="<?php p($languageObj->getProperty('WDay5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Friday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_friday" SIZE="20" value="<?php p($languageObj->getProperty('WDay6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Saturday", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_saturday" SIZE="20" value="<?php p($languageObj->getProperty('WDay7')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  echo $translator->trans('Please enter the translation for week day short names.', array(), 'languages'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Su", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_sunday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Mo", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_monday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Tu", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_tuesday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("We", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_wednesday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Th", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_thursday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Fr", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_friday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Sa", array(), 'languages'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_short_saturday" SIZE="20" value="<?php p($languageObj->getProperty('ShortWDay7')); ?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2"><?php  echo $translator->trans('Please enter the translation for time units.', array(), 'languages'); ?></TD>
</TR>
	<?php
	for ($i = 0; $i < count($q_defaultTimeUnits); $i++) {
	    if (count($q_timeUnits) > 0) {
            $value = $q_timeUnits[$i]['Name'];
	    }
	    else {
	        $value = $q_defaultTimeUnits[$i]['Name'];
	    }
	   ?>
	   <TR>
		  <TD ALIGN="RIGHT"><?php p(htmlspecialchars($q_defaultTimeUnits[$i]['Name']));?></TD>
		  <TD><INPUT TYPE="TEXT" class="input_text" NAME="<?php p(htmlspecialchars($q_defaultTimeUnits[$i]['Unit']));?>" SIZE="20" VALUE="<?php  p(htmlspecialchars($value)); ?>"></TD>
	   </TR>
	   <?php
	} ?>
	<TR>

	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  echo $translator->trans('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.forms.language_form.f_language_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
