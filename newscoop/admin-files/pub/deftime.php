<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SubscriptionDefaultTime.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Country.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), 1);
}

$defaultTimes = SubscriptionDefaultTime::GetSubscriptionDefaultTimes(null, $Pub);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top(getGS("Subscription Settings by Country"), array("Pub" => $publicationObj));

?>
<p>
<TABLE class="action_buttons">
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/countryadd.php?Pub=<?php p($Pub); ?>&Language=<?php p($Language); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/countryadd.php?Pub=<?php p($Pub); ?>&Language=<?php p($Language); ?>" ><B><?php  putGS("Set subscription settings for a country"); ?></B></A></TD>
</TR>
</TABLE>

<?php camp_html_display_msgs(); ?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP" rowspan="2"><B><?php  putGS("Country<BR><SMALL>(click to edit)</SMALL>"); ?></B></TD>
	<td colspan="2"><?php putGS('Default time period'); ?> (<?php p($pubTimeUnit->getName()); ?>):</td>
	<TD ALIGN="LEFT" VALIGN="TOP" rowspan="2"><B><?php  putGS("Delete"); ?></B></TD>
</TR>
<tr class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("trial subscription"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("paid subscription"); ?></B></TD>
</tr>
<?php
$color = 0;
foreach ($defaultTimes as $time) {
	$country = new Country($time->getCountryCode(), $Language);
	?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD>
    	<A HREF="/<?php p($ADMIN); ?>/pub/editdeftime.php?Pub=<?php p($Pub); ?>&CountryCode=<?php  p($time->getCountryCode()); ?>&Language=<?php p($Language); ?>"><?php p(htmlspecialchars($country->getName())); ?> (<?php p(htmlspecialchars($country->getCode())); ?>)</A>
		</TD>
		<TD ALIGN="center">
			<?php p(htmlspecialchars($time->getTrialTime())); ?>
		</TD>
		<TD ALIGN="center">
			<?php p(htmlspecialchars($time->getPaidTime())); ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php p($ADMIN); ?>/pub/do_deldeftime.php?Pub=<?php p($Pub); ?>&CountryCode=<?php  p($time->getCountryCode()); ?>&Language=<?php p($Language); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the subscription settings for $1?', "&quot;".htmlspecialchars($publicationObj->getName()).':'.htmlspecialchars($time->getCountryCode())."&quot;"); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete'); ?>" TITLE="<?php  putGS('Delete'); ?>" ></A>
		</TD>
	</TR>
<?php
}
?>	<TR><TD COLSPAN="2" NOWRAP>
</TABLE>

<?php camp_html_copyright_notice(); ?>
