<?php
camp_load_translation_strings("user_subscription_sections");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Subscription.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/SubscriptionSection.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

if (!$g_user->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to change subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', null);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', null);
$f_subscription_start_date = Input::Get('f_subscription_start_date');
$f_subscription_days = Input::Get('f_subscription_days', 'int', 0);
$f_subscription_paid_days = Input::Get('f_subscription_paid_days', 'int', 0);

$publicationObj =& new Publication($f_publication_id);
$languageObj =& new Language($publicationObj->getDefaultLanguageId());
$manageUser =& new User($f_user_id);
$subscription =& new Subscription($f_subscription_id);
$isPaid = false;
if ($subscription->getType() == 'P') {
	$isPaid = true;
}

$subscriptionSections = SubscriptionSection::GetSubscriptionSections($f_subscription_id, $f_section_number, $f_language_id);

if (!$isPaid) {
	$f_subscription_paid_days = $f_subscription_days;
}
foreach ($subscriptionSections as $section) {
	$section->setStartDate($f_subscription_start_date);
	$section->setDays($f_subscription_days);
	$section->setPaidDays($f_subscription_paid_days);
}

header("Location: /$ADMIN/users/subscriptions/sections/?f_publication_id=$f_publication_id&f_user_id=$f_user_id&f_subscription_id=$f_subscription_id");
exit;

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'",
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Subscribed sections").": ".$publicationObj->getName(), "/$ADMIN/users/subscriptions/sections/?f_user_id=$f_user_id&f_subscription_id=$f_subscription_id&f_publication_id=$f_publication_id");
$crumbs[] = array(getGS("Updating subscription"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Changing subscription"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('The subscription could not be updated.'); ?></LI></BLOCKQUOTE></TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/users/subscriptions/sections/change.php?f_publication_id=<?php p($f_publication_id); ?>&f_user_id=<?php  p($f_user_id); ?>&f_subscription_id=<?php p($f_subscription_id); ?>&f_section_number=<?php p($f_section_number); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
