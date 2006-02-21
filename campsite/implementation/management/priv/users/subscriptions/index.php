<?PHP
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("user_subscriptions");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Subscription.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_subscription_offset = Input::Get('f_subscription_offset', 'int', 0, true);
if ($f_subscription_offset < 0) {
	$f_subscription_offset = 0;
}
$ItemsPerPage = 20;
$manageUser =& new User($f_user_id);

$publications = Publication::GetPublications();
$numSubscriptions = Subscription::GetNumSubscriptions(null, $f_user_id);
$subscriptions = Subscription::GetSubscriptions(null, $f_user_id,
	array("ORDER BY" => array("Id" => "DESC"),
		  "LIMIT" => array("START" => $f_subscription_offset, "MAX_ROWS" => $ItemsPerPage)));

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'",
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
echo camp_html_breadcrumbs($crumbs);

if (sizeof($publications) > $numSubscriptions) {
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
	<TD><A HREF="add.php?f_user_id=<?php p($f_user_id); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL'];?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="add.php?f_user_id=<?php p($f_user_id); ?>" ><B><?php  putGS("Add new subscription"); ?></B></A></TD>
</TR>
</TABLE>
<?php
} // if (sizeof($publications) > $numSubscriptions)
?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Publication<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Left to pay"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Type"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Active"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
</TR>

<?php
$color=0;
foreach ($subscriptions as $subscription) { ?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<TD>
		<?php $publication =& new Publication($subscription->getPublicationId()); ?>
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/sections/?f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>&f_publication_id=<?php p($subscription->getPublicationId()); ?>&f_user_id=<?php p($f_user_id); ?>"><?php p(htmlspecialchars($publication->getName())); ?></A>&nbsp;
	</TD>

	<TD >
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/topay.php?f_user_id=<?php p($f_user_id); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>"><?php  p(htmlspecialchars($subscription->getToPay()).' '.htmlspecialchars($subscription->getCurrency())); ?>
	</TD>

	<TD >
		<?php
		$sType = $subscription->getType();
		if ($sType == 'T') {
			putGS("Trial subscription");
		} else {
			putGS("Paid subscription");
		}
		?>
	</TD>

	<TD ALIGN="CENTER">
	<?php if ($subscription->isActive()) { ?>
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/do_status.php?f_user_id=<?php p($f_user_id); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to deactivate the subscription?'); ?>');">Yes</A>
	<?php } else { ?>
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/do_status.php?f_user_id=<?php p($f_user_id); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to activate the subscription?'); ?>');">No</A>
	<?php } ?>
	</TD>
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/users/subscriptions/do_del.php?f_user_id=<?php p($f_user_id); ?>&f_subscription_id=<?php p($subscription->getSubscriptionId()); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete subscriptions to $1', htmlspecialchars($publication->getName())); ?>" TITLE="<?php  putGS('Delete subscriptions to $1', htmlspecialchars($publication->getName())); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the subscription to the publication $1?', htmlspecialchars($publication->getName())); ?>');"></A>
	</TD>
</TR>
<?php
}
?>
<TR>
	<TD COLSPAN="2" NOWRAP>
	<?php  if ($f_subscription_offset > 0) { ?>
		<B><A HREF="index.php?f_user_id=<?php p($f_user_id); ?>&f_subscription_offset=<?php p(max(0, ($f_subscription_offset - $ItemsPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
	<?php  } ?>

	<?php  if ($numSubscriptions > ($f_subscription_offset + $ItemsPerPage)) { ?>
		| <B><A HREF="index.php?f_user_id=<?php p($f_user_id); ?>&f_subscription_offset=<?php  p($f_subscription_offset + $ItemsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
	<?php  } ?>
	</TD>
</TR>
</TABLE>
<?php camp_html_copyright_notice(); ?>
