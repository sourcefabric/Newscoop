<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("user_subscription_sections");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/SubscriptionSection.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Language.php');
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
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_subscription_start_date = Input::Get('f_subscription_start_date');
$f_subscription_days = Input::Get('f_subscription_days');
$success = true;

$publicationObj =& new Publication($f_publication_id);
$languageObj =& new Language($publicationObj->getDefaultLanguageId());
$manageUser =& new User($f_user_id);
$errorMsgs = array();

if ($f_section_number != 0) {
	$subscriptionSection =& new SubscriptionSection($f_subscription_id, $f_section_number);
	$columns = array('StartDate' => $f_subscription_start_date, 
					 'Days' => $f_subscription_days, 
					 'PaidDays' => $f_subscription_days);
	if (!$subscriptionSection->exists()) {
		$success = $subscriptionSection->create($columns);
	}
	if (!$success) {
 		$errorMsgs[] = getGS('The section could not be added.').' '.getGS("Please check if there isn't another subscription with the same section."); 		
	}
} else {
	$sections = Section::GetUniqueSections($f_publication_id);
	$columns = array('StartDate' => $f_subscription_start_date, 
					 'Days' => $f_subscription_days, 
					 'PaidDays' => $f_subscription_days);
	foreach ($sections as $section) {
		$subscriptionSection =& new SubscriptionSection($f_subscription_id, $section['id']);
		if (!$subscriptionSection->exists()) {
			$success &= $subscriptionSection->create($columns);
		}
	}
	if (!$success) {
		$errorMsgs[] = getGS('The sections could not be added successfully. Some of them were already added !');
	}
}
if ($success) {
	header("Location: /$ADMIN/users/subscriptions/sections/?f_user_id=$f_user_id&f_subscription_id=$f_subscription_id&f_publication_id=$f_publication_id");
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'", 
			"/$ADMIN/users/edit.php?User=$User&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Subscribed sections").": ".$publicationObj->getName(), "/$ADMIN/users/subscriptions/sections/?f_user_id=$f_user_id&f_subscription_id=$f_subscription_id&f_publication_id=$f_publication_id");
$crumbs[] = array(getGS("Adding sections"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding sections to subscription"); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/users/subscriptions/sections/add.php?f_publication_id=<?php p($f_publication_id); ?>&f_user_id=<?php  p($f_user_id); ?>&f_subscription_id=<?php p($f_subscription_id); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
