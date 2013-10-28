<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SubscriptionDefaultTime.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Country.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error($translator->trans("You do not have the right to manage publications.", array(), 'pub'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Language = Input::Get('Language', 'int', 1);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), $publicationObj->getLanguageId());
if (!$pubTimeUnit->exists()) {
	$pubTimeUnit = new TimeUnit($publicationObj->getTimeUnit(), 1);
}

$defaultTimes = SubscriptionDefaultTime::GetSubscriptionDefaultTimes(null, $Pub);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_content_top($translator->trans("Subscription Settings by Country", array(), 'pub'), array("Pub" => $publicationObj));

?>
<p>
<TABLE class="action_buttons">
<TR>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/countryadd.php?Pub=<?php p($Pub); ?>&Language=<?php p($Language); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/pub/countryadd.php?Pub=<?php p($Pub); ?>&Language=<?php p($Language); ?>" ><B><?php  echo $translator->trans("Set subscription settings for a country", array(), 'pub'); ?></B></A></TD>
</TR>
</TABLE>

<?php camp_html_display_msgs(); ?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP" rowspan="2"><B><?php  echo $translator->trans("Country<BR><SMALL>(click to edit)</SMALL>", array(), 'pub'); ?></B></TD>
	<td colspan="2"><?php echo $translator->trans('Default time period', array(), 'pub'); ?> (<?php p($pubTimeUnit->getName()); ?>):</td>
	<TD ALIGN="LEFT" VALIGN="TOP" rowspan="2"><B><?php  echo $translator->trans("Delete"); ?></B></TD>
</TR>
<tr class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  echo $translator->trans("trial subscription", array(), 'pub'); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  echo $translator->trans("paid subscription", array(), 'pub'); ?></B></TD>
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
			<A HREF="/<?php p($ADMIN); ?>/pub/do_deldeftime.php?Pub=<?php p($Pub); ?>&CountryCode=<?php  p($time->getCountryCode()); ?>&Language=<?php p($Language); ?>&<?php echo SecurityToken::URLParameter(); ?>" onclick="return confirm('<?php echo $translator->trans('Are you sure you want to delete the subscription settings for $1?', "&quot;".htmlspecialchars($publicationObj->getName()).':'.htmlspecialchars($time->getCountryCode())."&quot;"); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  echo $translator->trans('Delete'); ?>" TITLE="<?php  echo $translator->trans('Delete'); ?>" ></A>
		</TD>
	</TR>
<?php
}
?>	<TR><TD COLSPAN="2" NOWRAP>
</TABLE>

<?php camp_html_copyright_notice(); ?>
