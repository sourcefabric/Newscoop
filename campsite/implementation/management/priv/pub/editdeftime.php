<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SubscriptionDefaultTime.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Country.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/TimeUnit.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to edit publication information."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1, true);
$CountryCode = Input::Get('CountryCode');
$publicationObj =& new Publication($Pub);
$pubTimeUnit =& new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit =& new TimeUnit($publicationObj->getTimeUnit(), 1);
}

$defaultTime =& new SubscriptionDefaultTime($CountryCode, $Pub);
$country =& new Country($CountryCode, $Language);

$crumbs = array(getGS("Subscriptions") => "deftime.php?Pub=$Pub");
camp_html_content_top(getGS("Change subscription default time"), array("Pub" => $publicationObj), true, false, $crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_editdeftime.php"  >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<INPUT TYPE=HIDDEN NAME="Pub" VALUE="<?php p($Pub); ?>">
<INPUT TYPE=HIDDEN NAME="CountryCode" VALUE="<?php p($CountryCode); ?>">
<INPUT TYPE=HIDDEN NAME="Language" VALUE="<?php p($Language); ?>">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Country"); ?>:</TD>
	<TD>
	<?php p(htmlspecialchars($country->getName()." (".$country->getCode().")")); ?>
	</TD>
</TR>
<tr>
	<td colspan="2" align="left"><b><?php putGS('Default time period'); ?>:</b></td>
</tr>
<TR>
	<TD ALIGN="RIGHT" >- <?php  putGS("trial subscription"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cTrialTime" VALUE="<?php p($defaultTime->getTrialTime()); ?>" SIZE="5" MAXLENGTH="5">
	<?php p($pubTimeUnit->getName()); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" >- <?php  putGS("paid subscription"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cPaidTime" VALUE="<?php p($defaultTime->getPaidTime()); ?>" SIZE="5" MAXLENGTH="5">
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
