<?php
camp_load_translation_strings("user_subscriptions");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Subscription.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

if (!$g_user->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to add subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_add_sections_now = Input::Get('f_add_sections_now', 'string', 'Y');
$f_language_set = Input::Get('f_language_set', 'string', 'select');
$f_subscription_start_date = Input::Get('f_subscription_start_date', 'string', date("Y-m-d"));
$f_subscription_type = Input::Get('f_subscription_type', 'string', 'PN');
$f_subscription_days = Input::Get('f_subscription_days', 'int', 0);

if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
	$uriPath = strtok($_SERVER['HTTP_REFERER'], "?");
} elseif ($g_user->hasPermission('ManageUsers')) {
	$uriPath = "/$ADMIN/users/edit.php";
} else {
	$uriPath = "/$ADMIN/users/subscriptions";
}
$inSubscriptions = (strstr($uriPath, '/subscriptions') != '')
					|| !$g_user->hasPermission('ManageUsers');

$manageUser =& new User($f_user_id);
$publications = Publication::GetPublications();
$subscriptions = Subscription::GetSubscriptions(null, $f_user_id);
if (sizeof($subscriptions) > 0) {
	$subscriptions_ids = array();
	foreach ($subscriptions as $subscription) {
		$subscriptions_ids[] = $subscription->getPublicationId();
	}
	$remaining_publications = array();
	foreach ($publications as $publication) {
		if (!in_array($publication->getPublicationId(), $subscriptions_ids)) {
			$remaining_publications[] = $publication;
		}
	}
	$publications = $remaining_publications;
}

if (sizeof($publications) <= 0) {
	camp_html_display_error('Subscriptions exist for all available publications!', "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
	exit;
}

if ($f_publication_id > 0) {
	$default_publication = new Publication($f_publication_id);
} elseif (sizeof($publications) > 0) {
	$default_publication = $publications[0];
} else {
	$default_publication = null;
}
if ($default_publication != null) {
	$languages = $default_publication->getLanguages();
} else {
	$languages = array();
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Subscribers"), "/$ADMIN/users/?uType=Subscribers");
$crumbs[] = array(getGS("Account") . " '".$manageUser->getUserName()."'",
			"/$ADMIN/users/edit.php?User=$f_user_id&uType=Subscribers");
$crumbs[] = array(getGS("Subscriptions"), "/$ADMIN/users/subscriptions/?f_user_id=$f_user_id");
$crumbs[] = array(getGS("Add new subscription"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");
?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php"  onsubmit="return <?php camp_html_fvalidate(); ?>;">
<input type="hidden" name="f_in_subscriptions" value="<?php print_r($inSubscriptions); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Publication"); ?>:</TD>
	<TD>
		<SELECT NAME="f_publication_id" class="input_select" ONCHANGE="if (this.options[this.selectedIndex].value != <?php p($f_publication_id); ?>) { this.form.action = 'add.php'; this.form.submit(); }">
		<?php
		foreach ($publications as $publication) {
			camp_html_select_option($publication->getPublicationId(), $f_publication_id, $publication->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>
<?php if (count($languages) == 0) { ?>
	<input type="hidden" name="f_language_set" value="all">
<?php } else { ?>
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Languages"); ?>:</TD>
	<TD>
		<select name="f_language_set" class="input_select" onchange="ToggleRowVisibility('select_languages'); langBox = this.form['f_language_id[]']; if (this.options[selectedIndex].value == 'all' && langBox.selectedIndex < 0) { langBox.selectedIndex = 0; }">
<?php
		$language_sets = array('select'=>getGS('Individual languages'), 'all'=>getGS('Regardless of the language'));
		foreach ($language_sets as $language_set_id=>$language_set_description) {
			camp_html_select_option($language_set_id, $f_language_set, $language_set_description);
		}
?>
		</select>
	</td>
</tr>
<tr id="select_languages" <?php if ($f_language_set == 'all') { ?>style="display: none;"<?php } ?>>
	<td></td>
	<td>
		<select name="f_language_id[]" multiple size="3" class="input_select" alt="selectm|1|*" emsg="<?php putGS("Please select one or more languages."); ?>">
		<?php
		foreach ($languages as $language) {
			camp_html_select_option($language->getLanguageId(), '', $language->getName() . ' (' . $language->getNativeName() . ')');
		}
		?>
		</select>
	</TD>
</TR>
<?php } ?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Sections"); ?>:</TD>
	<TD>
		<SELECT NAME="f_add_sections_now" class="input_select">
<?php
		$add_sections_times = array('Y'=>getGS('Add sections now'), 'N'=>getGS('Add sections later'));
		foreach ($add_sections_times as $add_sections_times_id=>$add_sections_times_description) {
			camp_html_select_option($add_sections_times_id, $f_add_sections_now, $add_sections_times_description);
		}
?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Start"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_start_date" SIZE="10" VALUE="<?php echo $f_subscription_start_date; ?>" MAXLENGTH="10" alt="date|yyyy/mm/dd|-" emsg="<?php putGS("You must input a valid date."); ?>"><?php  putGS('(YYYY-MM-DD)'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Subscription Type"); ?>:</TD>
	<TD>
	<SELECT NAME="f_subscription_type" class="input_select">
<?php
	$subscription_types = array('PN'=>getGS('Paid (confirm payment now)'),
								'PL'=>getGS('Paid (payment will be confirmed later)'),
								'T'=>getGS('Trial'));
	foreach ($subscription_types as $subscription_type_id=>$subscription_type_description) {
		camp_html_select_option($subscription_type_id, $f_subscription_type, $subscription_type_description);
	}
?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Days"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_subscription_days" VALUE="<?php if ($f_subscription_days > 0) echo $f_subscription_days; ?>" SIZE="5" MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Days"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="f_subscription_active" CHECKED class="input_checkbox"></TD>
	<TD>
		<?php putGS("Active"); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="HIDDEN" NAME="f_user_id" VALUE="<?php p($f_user_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
