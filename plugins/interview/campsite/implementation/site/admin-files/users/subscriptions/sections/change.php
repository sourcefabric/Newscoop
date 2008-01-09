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

$publicationObj = new Publication($f_publication_id);
$languageObj = new Language($publicationObj->getDefaultLanguageId());
$manageUser = new User($f_user_id);
$subscription = new Subscription($f_subscription_id);
$isPaid = false;
if ($subscription->getType() == 'P') {
	$isPaid = true;
}

$subscriptionSections = SubscriptionSection::GetSubscriptionSections($f_subscription_id,
							$f_section_number, $f_language_id);
$subscriptionSection = array_pop($subscriptionSections);
if ($f_section_number > 0) {
	if ($f_language_id > 0) {
		$subscriptionSectionLanguage = new Language($f_language_id);
		$languageName = $subscriptionSectionLanguage->getName();
	} else {
		$languageName = '-- ' . getGS('All languages') . ' --';
	}
} else {
	$languageName = getGS('N/A');
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'",
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Subscribed sections").": ".$publicationObj->getName(), "/$ADMIN/users/subscriptions/sections/?f_user_id=$f_user_id&f_subscription_id=$f_subscription_id&f_publication_id=$f_publication_id");
$crumbs[] = array(getGS("Change subscription"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");
?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_change.php"  onsubmit="return <?php camp_html_fvalidate(); ?>;">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change subscription"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<?php if (empty($subscriptionSection)) { ?>
	<tr>
		<td align="center">
			<BLOCKQUOTE>
			<LI><?php  putGS("There are no sections in this publication."); ?></LI>
			</BLOCKQUOTE>
			<INPUT type="button" class="button" value="<?php putGS("OK"); ?>" onclick="location.href='<?php echo "/$ADMIN/users/subscriptions/sections/?f_publication_id=$f_publication_id&f_user_id=$f_user_id&f_subscription_id=$f_subscription_id"; ?>';">
		</td>
	</tr>
	<?php
} else { ?>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Section"); ?>:</TD>
	<TD>
	<?php
	if ($f_section_number > 0) {
		p(htmlspecialchars($subscriptionSection->getProperty('Name')));
	} else {
		putGS("-- ALL SECTIONS --");
	}
	?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("Language"); ?>:</TD>
	<TD><?php p($languageName); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Start"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_start_date" SIZE="10" VALUE="<?php  p(htmlspecialchars($subscriptionSection->getStartDate())); ?>" MAXLENGTH="10" alt="date|yyyy/mm/dd|-" emsg="<?php putGS("You must input a valid date."); ?>"> <?php  putGS('(YYYY-MM-DD)'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Days"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_days" SIZE="5" VALUE="<?php p(htmlspecialchars($subscriptionSection->getDays())); ?>"  MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", getGS("Days")); ?>">
	</TD>
</TR>
<?php  if ($isPaid) { ?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Paid Days"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_paid_days" SIZE="5" VALUE="<?php  p($subscriptionSection->getPaidDays()); ?>" MAXLENGTH="5" alt="number|0|0|1000000000" emsg="<?php putGS("You must input a number greater or equal to 0 into the $1 field.", getGS("Paid Days")); ?>">
	</TD>
</TR>
<?php  } ?>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="HIDDEN" NAME="f_user_id" VALUE="<?php p($f_user_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_subscription_id" VALUE="<?php p($f_subscription_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php p($f_language_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
<?php } ?>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>
