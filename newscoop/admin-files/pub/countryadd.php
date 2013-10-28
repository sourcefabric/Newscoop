<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Country.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error($translator->trans("You do not have the right to manage publications.", array(), 'pub'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1, true);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), 1);
}

$countries = Country::GetCountries($Language);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

$crumbs = array($translator->trans("Subscriptions") => "deftime.php?Pub=$Pub&Language=$Language");
camp_html_content_top($translator->trans("Set subscription settings for a country", array(), 'pub'), array("Pub" => $publicationObj), true, false, $crumbs);
?>

<P>
<FORM METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_countryadd.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<?php p($Pub); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  echo $translator->trans("Country"); ?>:</TD>
	<TD>
    <SELECT NAME="cCountryCode" class="input_select" alt="select" emsg="<?php echo $translator->trans('You must select a country.'); ?>">
    <OPTION></OPTION>
	<?php
	foreach ($countries as $country) { ?>
	    <OPTION VALUE="<?php  p(htmlspecialchars($country->getCode())); ?>"><?php p(htmlspecialchars($country->getName())); ?>
	    <?php
    }
	?>
	</SELECT>
	</TD>
</TR>
<tr>
	<td colspan="2" align="left"><b><?php echo $translator->trans('Default time period', array(), 'pub'); ?>:</b></td>
</tr>
<TR>
	<TD ALIGN="RIGHT" >- <?php  echo $translator->trans("trial subscription", array(), 'pub'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cTrialTime" VALUE="1" SIZE="5" MAXLENGTH="5" alt="number|0|1|100000" emsg="<?php echo $translator->trans("You must input a number greater than 0 into the $1 field.", array('$1' => "&quot;".$translator->trans("trial subscription", array(), 'pub')."&quot;")); ?>">
	<?php p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" >- <?php  echo $translator->trans("paid subscription", array(), 'pub'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPaidTime" VALUE="1" SIZE="5" MAXLENGTH="5" alt="number|0|1|100000" emsg="<?php echo $translator->trans("You must input a number greater than 0 into the $1 field.", array('$1' => "&quot;".$translator->trans("paid subscription", array(), 'pub')."&quot;")); ?>">
	<?php p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  echo $translator->trans('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  echo $translator->trans('Cancel'); ?>" ONCLICK="location.href='/admin/pub/deftime.php?Pub=<?php  p($Pub); ?>&Language=<?php  p($Language); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>
