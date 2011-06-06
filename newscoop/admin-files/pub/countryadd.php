<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Country.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), 1);
}

$countries = Country::GetCountries($Language);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

$crumbs = array(getGS("Subscriptions") => "deftime.php?Pub=$Pub&Language=$Language");
camp_html_content_top(getGS("Set subscription settings for a country"), array("Pub" => $publicationObj), true, false, $crumbs);
?>

<P>
<FORM METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/do_countryadd.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<INPUT TYPE="HIDDEN" NAME="cPub" VALUE="<?php p($Pub); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Country"); ?>:</TD>
	<TD>
    <SELECT NAME="cCountryCode" class="input_select" alt="select" emsg="<?php putGS('You must select a country.'); ?>">
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
	<td colspan="2" align="left"><b><?php putGS('Default time period'); ?>:</b></td>
</tr>
<TR>
	<TD ALIGN="RIGHT" >- <?php  putGS("trial subscription"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cTrialTime" VALUE="1" SIZE="5" MAXLENGTH="5" alt="number|0|1|100000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "&quot;".getGS("trial subscription")."&quot;"); ?>">
	<?php p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" >- <?php  putGS("paid subscription"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPaidTime" VALUE="1" SIZE="5" MAXLENGTH="5" alt="number|0|1|100000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "&quot;".getGS("paid subscription")."&quot;"); ?>">
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
<?php camp_html_copyright_notice(); ?>
