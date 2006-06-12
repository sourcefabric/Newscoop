<?PHP
camp_load_translation_strings("user_subscriptions");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Subscription.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

if (!$g_user->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to delete subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id');

$manageUser =& new User($f_user_id);
$subscription =& new Subscription($f_subscription_id);
$publication =& new Publication($subscription->getPublicationId());

if ($subscription->delete()) {
	$uriPath = strtok($_SERVER['HTTP_REFERER'], "?");
	$inSubscriptions = (strstr($uriPath, '/subscriptions') != '')
						|| !$g_user->hasPermission('ManageUsers');
	if ($inSubscriptions) {
		$location = "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id";
	} else {
		$location = "/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers";
	}
	header("Location: $location");
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'",
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Delete subscription"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Delete subscription"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('The subscription could not be deleted.'); ?></LI></BLOCKQUOTE></TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/users/subscriptions/?f_user_id=<?php p($f_user_id); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
