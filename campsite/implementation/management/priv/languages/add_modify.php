<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("languages");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/localizer/Localizer.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$languageId = Input::Get('Lang', 'int', 0, true); 
$Back = Input::Get('Back', 'string', null, true);

$editMode = ($languageId != 0);

if (!$User->hasPermission('ManageLanguages')) {
    if (!$editMode) {
	   camp_html_display_error(getGS("You do not have the right to add languages."));
    }
    else {
       camp_html_display_error(getGS("You do not have the right to edit languages."));
    }
	exit;
}

$q_defaultTimeUnits = $Campsite['db']->GetAll("SELECT * FROM TimeUnits WHERE IdLanguage=1");
$numTimeUnits = 0;
if ($editMode) {
    $q_timeUnits = $Campsite['db']->GetAll("SELECT * FROM TimeUnits WHERE IdLanguage=$languageId");
}

$languageObj =& new Language($languageId);

//query ("SELECT * FROM Languages WHERE Id=$Lang", 'l');

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Languages"), "/$ADMIN/languages");
if ($editMode) { 
    $crumbs[] = array(getGS("Edit language"), "");
} else { 
    $crumbs[] = array(getGS("Add new language"), "");
} 
$breadcrumbs = camp_html_breadcrumbs($crumbs);

?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<TITLE><?php  putGS("Add new language"); ?></TITLE>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
</HEAD>

<BODY>

<?php echo $breadcrumbs; ?>
<!--<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	   <?php if ($editMode) { ?>
	       <?php  putGS("Edit language"); ?>
	   <?php } else { ?>
	       <?php  putGS("Add new language"); ?>
	   <?php } ?>
	</TD>

	<TD ALIGN=RIGHT>
        <TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
	    <TR>
	       <TD><A HREF="/admin/languages/" class="breadcrumb" ><?php  putGS("Languages");  ?></A></TD>
        </TR>
        </TABLE>
    </TD>
</TR>
</TABLE>
-->
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add_modify.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<?php if ($editMode) { ?>
<input type="hidden" name="cLang" value="<?php p($languageObj->getLanguageId()); ?>">
<?php } ?>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
	   <?php if ($editMode) { ?>
	       <B><?php  putGS("Edit language"); ?></B>
	   <?php } else { ?>
           <B><?php  putGS("Add new language"); ?></B>
       <?php } ?>	   
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="32" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>" value="<?php p($languageObj->getProperty('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Native name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cOrigName" SIZE="32" MAXLENGTH="32" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Native name')); ?>" value="<?php p($languageObj->getProperty('OrigName')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Code"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cCode" SIZE="2" MAXLENGTH="2" alt="length|2|2" emsg="<?php  putGS('You must complete the $1 field.', getGS('Code')); ?>" value="<?php p($languageObj->getProperty('Code')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Code page"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cCodePage" SIZE="32" MAXLENGTH="32" alt="blank" emsg="<?php  putGS('You must complete the $1 field.', getGS('Code page')); ?>" value="<?php p($languageObj->getProperty('CodePage')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  putGS('Please enter the translation for month names.'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("January"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth1" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("February"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth2" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("March"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth3" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("April"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth4" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("May"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth5" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("June"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth6" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("July"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth7" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month7')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("August"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth8" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month8')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("September"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth9" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month9')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("October"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth10" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month10')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("November"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth11" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month11')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("December"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cMonth12" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('Month12')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  putGS('Please enter the translation for week day names.'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Sunday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay1" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Monday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay2" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Tuesday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay3" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Wednesday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay4" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Thursday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay5" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Friday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay6" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Saturday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cWDay7" SIZE="20" MAXLENGTH="20" value="<?php p($languageObj->getProperty('WDay7')); ?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2"><?php  putGS('Please enter the translation for time units.'); ?></TD>
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
		  <TD><INPUT TYPE="TEXT" class="input_text" NAME="<?php p(htmlspecialchars($q_defaultTimeUnits[$i]['Unit']));?>" SIZE="20" MAXLENGTH="20" VALUE="<?php  p(htmlspecialchars($value)); ?>"></TD>
	   </TR>
	   <?php 
	} ?> 	
	<TR>
	
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php  print encHTML($Back); ?>">
        <?php  if ($Back != "") { ?>
        <INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php  print $Back; ?>'">
        <?php  } else { ?>
        <INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/languages/'">
        <?php  } ?>
        </DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
