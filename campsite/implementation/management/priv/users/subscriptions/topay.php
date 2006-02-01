<?php
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

if (!$User->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to change subscriptions status."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);

$manageUser =& new User($f_user_id);
$subscription =& new Subscription($f_subscription_id);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'", 
			"/$ADMIN/users/edit.php?User=$User&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Change subscription status"), "");
echo camp_html_breadcrumbs($crumbs);


?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_topay.php"  >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Update payment"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
    <TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Left to pay"); ?>:</TD>
	<TD>
        <INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_left_to_pay" VALUE="<?php  p($subscription->getToPay()); ?>" SIZE=10> <?php  p(htmlspecialchars($subscription->getCurrency())); ?>
    	</TD>
</TR>
    <TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_user_id" VALUE="<?php  p($f_user_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_subscription_id" VALUE="<?php  p($f_subscription_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/users/subscriptions/?f_user_id=<?php  p($f_user_id); ?>'">-->
    	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>
