<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, $canManage, $canDelete);

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Subscribers' ? 'Y' : 'N';

$crumbs = array();
$crumbs[] = array(getGS("Users"), ""); 
if ($uType == "Staff") { 
    $crumbs[] = array(getGS("Staff management"), ""); 
} else { 
    $crumbs[] = array(getGS("Subscriber management"), ""); 
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>
<p>
<table border="0" cellspacing="0" cellpadding="0" class="action_buttons">
<tr>
<?php
if ($canManage) {
	$addLink = "edit.php?" . get_user_urlparams(0, true, true);
?>
	<td><a href="<?php echo $addLink; ?>">
		<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0">
		<b><?php putGS("Add new user account"); ?></b></a></td>
<?php } ?>
	<td style="padding-left: 10px;">
		<a href="?<?php echo get_user_urlparams(0, false, true); ?>">
		<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/reset.png" border="0">
		<B><?php putGS("Reset search conditions"); ?></b></a></td>
</tr>
</table>
<p>
<table border="0" cellspacing="0" cellpadding="3" class="table_input" style="margin-bottom: 10px; margin-top: 5px; margin-left: 17px;">
<form method="POST" action="index.php">
<input type="hidden" name="uType" value="<?php p($uType); ?>">
<input type="hidden" name="userOffs" value="<?php p($userOffs); ?>">
<input type="hidden" name="lpp" value="<?php p($lpp); ?>">
<tr>
	<td style="padding-left: 10px;"><?php putGS("Full Name"); ?></td>
	<td><input type="text" name="full_name" value="<?php p(htmlspecialchars($full_name)); ?>" class="input_text" style="width: 150px;"></td>
	<td><?php putGS("User Name"); ?></td>
	<td><input type="text" name="user_name" value="<?php p(htmlspecialchars($user_name)); ?>" class="input_text" style="width: 70px;"></td>
	<td><?php putGS("E-Mail"); ?></td>
	<td><input type="text" name="email" value="<?php p(htmlspecialchars($email)); ?>" class="input_text" style="width: 150px;"></td>
	<td><input type="submit" name="submit_button" value="<?php putGS("Search"); ?>" class="button"></td>
</tr>
<?php if ($uType == "Subscribers") { ?>
<tr>
	<td colspan="11" align="center">
		<?php putGS("Subscription"); ?>&nbsp;
		<select name="subscription_how" class="input_select" style="width: 100px;">
		<?php 
		camp_html_select_option("expires", $subscription_how, getGS("expires")); 
		camp_html_select_option("starts", $subscription_how, getGS("starts")); 
		?>
		</select>
		<select name="subscription_when" class="input_select" style="width: 100px;">
		<?PHP
		camp_html_select_option("before", $subscription_when, getGS("before"));
		camp_html_select_option("after", $subscription_when, getGS("after"));
		camp_html_select_option("on", $subscription_when, getGS("on"));
		?>
		</select>
		<input type="text" name="subscription_date" value="<?php p(htmlspecialchars($subscription_date)); ?>" class="input_text" style="width: 100px;">
		&nbsp;<?php putGS('(yyyy-mm-dd)'); ?>&nbsp;&nbsp;
		<?php putGS("status"); ?>:
		<select name="subscription_status" class="input_select" style="width: 100px;">
		<option value=""></option>
		<?PHP
		camp_html_select_option("active", $subscription_status, getGS("active"));
		camp_html_select_option("inactive", $subscription_status, getGS("inactive"));
		?>
		</select>
	</td>
</tr>
<tr>
	<td colspan="11" align="center">
		<?php putGS("IP address"); ?>:
		<input type="text" class="input_text" name="StartIP1" size="3" maxlength="3" value="<?php if ($startIP1 != 0) echo $startIP1; ?>">.
		<input type="text" class="input_text" name="StartIP2" size="3" maxlength="3" value="<?php if ($startIP2 != 0) echo $startIP2; ?>">.
		<input type="text" class="input_text" name="StartIP3" size="3" maxlength="3" value="<?php if ($startIP3 != 0) echo $startIP3; ?>">.
		<input type="text" class="input_text" name="StartIP4" size="3" maxlength="3" value="<?php if ($startIP4 != 0) echo $startIP4; ?>">
		(<?php putGS("fill in from left to right at least one input box"); ?>)
	</td>
</tr>
<?php } ?>
</form>
</table>

<?php if ($resMsg != '') { ?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
<?php if ($res == 'OK') { ?>
	<td class="info_message">
<?php } else { ?>
	<td class="error_message">
<?php } ?>
		<?php echo $resMsg; ?>
	</td>
</tr>
</table>
<?php } ?>

<?php
$sql = "SELECT u.* FROM Users AS u";
if ($startIP1 != 0) {
	$sql .= " LEFT JOIN SubsByIP AS sip ON u.Id = sip.IdUser";
}
if ($subscription_date != "" || $subscription_status != "") {
	$sql .= " LEFT JOIN Subscriptions AS s ON u.Id = s.IdUser";
	if ($subscription_date != "")
		$sql .= " LEFT JOIN SubsSections AS ss ON s.Id = ss.IdSubscription";
}
$sql .= " WHERE u.Reader = '$isReader'";
if ($full_name != '')
	$sql .= " AND Name like '%" . mysql_escape_string($full_name) . "%'";
if ($user_name != '')
	$sql .= " AND UName like '%" . mysql_escape_string($user_name) . "%'";
if ($email != '')
	$sql .= " AND EMail like '%" . mysql_escape_string($email) . "%'";
if ($subscription_date != '') {
	$ss_field = "TO_DAYS(ss.StartDate) - TO_DAYS('$subscription_date')";
	if ($subscription_how == 'expires') {
		$ss_field .= " + Days";
	}
	switch ($subscription_when) {
	case 'before': $comp_sign = "<="; break;
	case 'after': $comp_sign = ">="; break;
	case 'on': $comp_sign = "="; break;
	}
	$sql .= " AND ($ss_field) $comp_sign 0";
}
if ($subscription_status != "")
	$sql .= " AND s.Active = '" . ($subscription_status == 'active' ? 'Y' : 'N') . "'";
if ($startIP1 != 0) {
	
	$minIP = $startIP1 * 256 * 256 * 256 + $startIP2 * 256 * 256 + $startIP3 * 256 + $startIP4;
	$maxIP2 = $startIP2 != 0 ? $startIP2 : 255;
	$maxIP3 = $startIP3 != 0 ? $startIP3 : 255;
	$maxIP4 = $startIP4 != 0 ? $startIP4 : 255;
	$maxIP = $startIP1 * 256 * 256 * 256 + $maxIP2 * 256 * 256 + $maxIP3 * 256 + $maxIP4;
	$sql .= " AND ((sip.StartIP >= $minIP AND sip.StartIP <= $maxIP)"
	     . " OR ((sip.StartIP - 1 + sip.Addresses) >= $minIP AND (sip.StartIP - 1 + sip.Addresses) <= $maxIP))";
}
if ($subscription_date != "") {
	$sql .= " GROUP BY s.Id";
}
$sql .= " ORDER BY Name ASC";
$res = $Campsite['db']->SelectLimit($sql, $lpp+1, $userOffs);
if (gettype($res) == 'object' && $res->NumRows() > 0) {
	$nr = $res->NumRows();
	$last = $nr > $lpp ? $lpp : $nr;
?><table border="0" cellspacing="1" cellpadding="3" class="table_list">
	<tr class="table_list_header">
		<td align="left" valign="top"><b><?php putGS("Full Name"); ?></b></td>
		<td align="left" valign="top"><b><?php putGS("User Name"); ?></b></td>
		<td align="left" valign="top"><b><?php putGS("E-Mail"); ?></b></td>
<?php if ($uType == "Subscribers" && $User->hasPermission("ManageSubscriptions")) { ?>
		<td align="left" valign="top"><b><?php putGS("Subscriptions"); ?></b></td>
<?php } ?>
<?php if ($canDelete) { ?>
		<td align="left" valign="top"><b><?php putGS("Delete"); ?></b></td>
<?php } ?>
	</TR>
<?php 
for($loop = 0; $loop < $last; $loop++) {
	$row = $res->FetchRow();
	$userId = $row['Id'];
	$rowClass = ($loop + 1) % 2 == 0 ? "list_row_even" : "list_row_odd";
?>
	<tr <?php echo "class=\"$rowClass\""; ?>>
		<td>
<?php
	if ($canManage)
		echo "<a href=\"edit.php?" . get_user_urlparams($userId, false, true) . "\">";
	echo htmlspecialchars($row['Name']);
	if ($canManage)
		echo "</a>";
	$old_user_name = $user_name;
	$user_name = $row['UName'];
?>
		</td>
		<td><?php echo htmlspecialchars($user_name); ?></TD>
<?php
	$user_name = $old_user_name;
	$old_email = $email;
	$email = $row['EMail'];
?>
		<td><?php echo htmlspecialchars($email); ?></td>
<?php if ($uType == "Subscribers" && $User->hasPermission("ManageSubscriptions")) { ?>
		<td><a href="<?php echo "/$ADMIN/users/subscriptions/?f_user_id=$userId"; ?>">
			<?php putGS("Subscriptions"); ?></td>
<?php } ?>
<?php
	$email = $old_email;
	if ($canDelete) { ?>
		<td align="center">
			<a href="/<?php echo $ADMIN; ?>/users/do_del.php?<?php echo get_user_urlparams($userId, false, true); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the user account $1 ?', $row['UName']); ?>');">
				<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" border="0" ALT="<?php putGS('Delete user $1', $row['UName']); ?>" title="<?php putGS('Delete user $1', $row['UName']); ?>">
			</a>
		</td>
<?php
	}
?>
	</tr>
<?php 
}
?>	<tr><td colspan="2" nowrap>
<?php
if ($userOffs > 0) {
	$oldUserOffs = $userOffs;
	$userOffs = $userOffs - $lpp;
?>		<b><a href="index.php?<?php echo get_user_urlparams(0); ?>">&lt;&lt; <?php putGS('Previous'); ?></a></b>
<?php
	$userOffs = $oldUserOffs;
}
if ($nr >= $lpp+1) {
	$userOffs += $lpp;
?>		 | <b><a href="index.php?<?php echo get_user_urlparams(0); ?>"><?php putGS('Next'); ?> &gt;&gt</a></b>
<?php  } ?>	</td></tr>
</table>
<?php  } else { ?><blockquote>
	<li><?php  putGS('User list is empty.'); ?></li>
</blockquote>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</body>

</html>
