<?php

check_basic_access($_REQUEST);
if (!$User->hasPermission("ManageSubscriptions") || !isset($editUser) || gettype($editUser) != 'object' || $editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'),$_SERVER['REQUEST_URI']);
	exit;
}

$publications = Publication::GetPublications();
$numSubscriptions = Subscription::GetNumSubscriptions(null, $editUser->getUserId());

?>
<table border="0" cellspacing="1" cellpadding="3" width="100%" class="table_list">
<tr class="table_list_header">
	<td colspan="5" align="left">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr class="table_list_header">
			<td align="left"><?php putGS("Subscriptions"); ?></td>
<?php
if (sizeof($publications) > $numSubscriptions) {
?>
			<td align="right" valign="center" nowrap>
				<?php $addURI = "/$ADMIN/users/subscriptions/add.php?f_user_id=".$editUser->getUserId(); ?>
				<a href="<?php echo $addURI; ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
				<a href="<?php echo $addURI; ?>"><B><?php putGS("Add new"); ?></B></A>
			</td>
<?php
} // if (sizeof($publications) > $numSubscriptions)
?>
		</tr>
		</table>
	</td>
</tr>
<?php
$subscriptions = Subscription::GetSubscriptions(null, $editUser->getUserId(), array("ORDER BY" => array("Id" => "DESC")));
if (count($subscriptions) > 0) {
	$color=0;
	?>
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Publication"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Left to pay"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" nowrap><B><?php  putGS("Type"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Active"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php
	foreach ($subscriptions as $subscription) {
		$publicationObj =& new Publication($subscription->getPublicationId());
?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD>
		<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/sections/?f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>&f_publication_id=<?php p($subscription->getPublicationId()); ?>&f_user_id=<?php echo $editUser->getUserId(); ?>"><?php p(htmlspecialchars($publicationObj->getName())); ?></A>&nbsp;
		</TD>
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/topay.php?f_user_id=<?php echo $editUser->getUserId(); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>">
			<?php  p(htmlspecialchars($subscription->getToPay())).' '.p(htmlspecialchars($subscription->getCurrency())); ?></A>
		</TD>
		<TD >
			<?php
			$sType = $subscription->getType();
			if ($sType == 'T') {
				putGS("Trial");
			} else {
				putGS("Paid");
			}
			?>
		</TD>
		<TD ALIGN="CENTER">
		<?php if ($subscription->isActive()) { ?>
			<a href="/<?php echo $ADMIN; ?>/users/subscriptions/do_status.php?f_user_id=<?php echo $editUser->getUserId(); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to deactivate the subscription?'); ?>');"><?php putGS('Yes'); ?></a>
		<?php } else { ?>
			<a href="/<?php echo $ADMIN; ?>/users/subscriptions/do_status.php?f_user_id=<?php echo $editUser->getUserId(); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to activate the subscription?'); ?>');"><?php putGS('No');?></a>
		<?php } ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/users/subscriptions/do_del.php?f_user_id=<?php echo $editUser->getUserId(); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the subscription to the publication $1?', htmlspecialchars($publicationObj->getName())); ?>');">
			<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete subscriptions to $1', htmlspecialchars($publicationObj->getName()) ); ?>" TITLE="<?php  putGS('Delete subscriptions to $1', htmlspecialchars($publicationObj->getName()) ); ?>"></A>
		</TD>
	</TR>
<?php
}
?>
<?php  } else { ?>
<tr class="list_row_odd"><td colspan="5"><?php  putGS('No subscriptions.'); ?></td></tr>
<?php  } ?>
</table>
<br>
