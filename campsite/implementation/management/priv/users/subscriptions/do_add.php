<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("user_subscriptions");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Subscription.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/SubscriptionSection.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to add subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id');
$f_subscription_active = Input::Get('f_subscription_active', 'string', '', true);
$f_subscription_type = Input::Get('f_subscription_type');
$f_subscription_start_date = Input::Get('f_subscription_start_date');
$f_add_sections_now = Input::Get('f_add_sections_now');
$f_subscription_days = Input::Get('f_subscription_days');

if ($f_subscription_active === "on") {
	$f_subscription_active = "Y";
} else {
	$f_subscription_active = "N";
}

$errorMsgs = array();
$manageUser =& new User($f_user_id);

$paidDays = 0;
if ( ($f_subscription_type == "PN") || ($f_subscription_type == "T") ) {
	$paidDays = $f_subscription_days;
}
$subsType = 'T';
if ($f_subscription_type != "T") {
	$subsType = 'P';
}

$subscription =& new Subscription();
$created = $subscription->create(array(
	'IdUser' => $f_user_id, 
	'IdPublication' => $f_publication_id, 
	'Active' => $f_subscription_active,
	'Type' => $f_subscription_type));

if (!$created) {
	$errorMsgs[] = getGS('The subscription could not be added.').' '.getGS("Please check if there isn't another subscription to the same publication."); 
}

if ($created && ($f_add_sections_now == 'Y')) {
	$columns = array('StartDate' => $f_subscription_start_date,
			  'Days' => $f_subscription_days,
			  'PaidDays' => $paidDays);
	echo "<pre>";print_r($columns);echo "</pre>";
	$created = SubscriptionSection::AddSubscriberToPublication(
		$subscription->getSubscriptionId(), 
		$f_publication_id, 
		$columns);
	if (!$created) { 
		$errorMsgs[] = getGS('The sections could not be added successfully. Some of them were already added !'); 
	}
	else {
		header("Location: /$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
		exit;
	}
} 

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'", 
			"/$ADMIN/users/edit.php?User=$User&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Adding subscription"), "");
echo camp_html_breadcrumbs($crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding subscription"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<?php 
	foreach ($errorMsgs as $errorMsg) { ?>
		<LI><?php p($errorMsg); ?></LI>
		<?PHP
	}
	?>
	</BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/users/subscriptions/add.php?f_user_id=<?php p($f_user_id); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
