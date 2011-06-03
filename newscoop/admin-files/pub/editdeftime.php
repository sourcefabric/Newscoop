<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SubscriptionDefaultTime.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Country.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to edit publication information."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1, true);
$CountryCode = Input::Get('CountryCode');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), 1);
}

$defaultTime = new SubscriptionDefaultTime($CountryCode, $Pub);
$country = new Country($CountryCode, $Language);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

$crumbs = array(getGS("Subscriptions") => "deftime.php?Pub=$Pub&Language=$Language");
camp_html_content_top(getGS("Change country subscription settings"), array("Pub" => $publicationObj), true, false, $crumbs);

?>
<P>
<FORM NAME="subscription_settings" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_editdeftime.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="CountryCode" VALUE="<?php p($CountryCode); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php p($Language); ?>">
<TR>
	<TD ALIGN="center" colspan="2">
		<b><?php  putGS("Country"); ?>: <?php p(htmlspecialchars($country->getName()." (".$country->getCode().")")); ?></b>
	</TD>
</TR>
<tr>
	<td colspan="2" align="left"><?php putGS('Default time periods:'); ?></td>
</tr>
<TR>
	<TD ALIGN="RIGHT" >- <?php  putGS("trial subscription"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="<?php p($defaultTime->getTrialTime()); ?>" SIZE="5" MAXLENGTH="5" class="input_text" alt="number|0|1|100000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "&quot;".getGS("trial subscription")."&quot;"); ?>">
	<?php p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" >- <?php  putGS("paid subscription"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="<?php p($defaultTime->getPaidTime()); ?>" SIZE="5" MAXLENGTH="5" class="input_text" alt="number|0|1|100000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "&quot;".getGS("paid subscription")."&quot;"); ?>">
	<?php p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/pub/deftime.php?Pub=<?php  p($Pub); ?>&Language=<?php  p($Language); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.forms.subscription_settings.cTrialTime.focus();
</script>
<?php camp_html_copyright_notice(); ?>
