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
	camp_html_display_error(getGS("You do not have the right to add subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id');

$manageUser =& new User($f_user_id);
$subscription =& new Subscription($f_subscription_id);
$publications = Publication::GetPublications();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'", 
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Add new subscription"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
</HEAD>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php"  onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new subscription"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Publication"); ?>:</TD>
	<TD>
		<SELECT NAME="f_publication_id" class="input_select">
		<?php 
		foreach ($publications as $publication) {
			camp_html_select_option($publication->getPublicationId(), '', $publication->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Sections"); ?>:</TD>
	<TD>
		<SELECT NAME="f_add_sections_now" class="input_select">
		<OPTION VALUE="Y"><?php  putGS('Add sections now'); ?></OPTION>
		<OPTION VALUE="N"><?php  putGS('Add sections later'); ?></OPTION>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Start"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_start_date" SIZE="10" VALUE="<?php  p(date("Y-m-d")); ?>" MAXLENGTH="10" alt="date|yyyy/mm/dd|-" emsg="<?php putGS("You must input a valid date."); ?>"><?php  putGS('(YYYY-MM-DD)'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Subscription Type"); ?>:</TD>
	<TD>
	<SELECT NAME="f_subscription_type" class="input_select">
	<OPTION VALUE="PN"><?php  putGS("Paid (confirm payment now)"); ?></OPTION>
	<OPTION VALUE="PL"><?php  putGS("Paid (payment will be confirmed later)"); ?></OPTION>
	<OPTION VALUE="T"><?php  putGS("Trial"); ?></OPTION>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Days"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_days" SIZE="5" MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Days"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="f_subscription_active" CHECKED class="input_checkbox"></TD>
	<TD>
		<?php putGS("Active"); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_user_id" VALUE="<?php p($f_user_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/users/subscriptions/?User=<?php  p($User); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
