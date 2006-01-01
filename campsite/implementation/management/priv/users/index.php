<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/SimplePager.php");
camp_load_language("api");

list($access, $User) = check_basic_access($_REQUEST);

if (Input::Get('reset_search', 'string', 'false', true) == 'true') {
	reset_user_search_parameters();
}
read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, $canManage, $canDelete);

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Subscribers' ? 'Y' : 'N';
$orderField = Input::Get('ordfld', 'string', 'fname');
$orderDir = Input::Get('orddir', 'string', 'asc');
$orderURLs = array('fname'=>'ordfld=fname&orddir=asc', 'uname'=>'ordfld=uname&orddir=asc',
	'cdate'=>'ordfld=cdate&orddir=asc');
$orderSigns = array('fname'=>'', 'uname'=>'', 'cdate'=>'');
$orderFields = array('fname'=>'Name', 'uname'=>'UName', 'cdate'=>'time_created');
if (!array_key_exists($orderField, $orderURLs)) {
	$orderField = 'fname';
	$orderDir = 'asc';
}
foreach($orderURLs as $field=>$fieldURL) {
	$dir = ($orderField == $field ? ($orderDir == 'asc' ? 'desc' : 'asc') : 'asc');
	$orderURLs[$field] = "ordfld=$field&orddir=$dir";
	if ($dir == 'desc') {
		$orderSigns[$field] = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/search_order_direction_down.png\" border=\"0\">";
	} else {
		$orderSigns[$field] = "<img src=\"".$Campsite["ADMIN_IMAGE_BASE_URL"]."/search_order_direction_up.png\" border=\"0\">";
	}
}

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
	<td style="padding-left: 20px;" valign="bottom">
		<a href="<?php echo $addLink; ?>">
		<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" border="0">
<?php
	if ($uType == "Staff") {
		echo "<b>" . getGS("Add new staff member") . "</b></a></td>";
	} else {
		echo "<b>" . getGS("Add new subscriber") . "</b></a></td>";
	}
}
?>
	</td>
<?php if (user_search_is_set()) { ?>
	<td style="padding-left: 20px;" valign="bottom">
		<a href="?reset_search=true<?php echo get_user_urlparams(0, false, true); ?>">
		<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/clear.png" border="0">
		<b><?php putGS("Reset search form"); ?></b>
		</a>
	</td>
<?php } ?>
</tr>
</table>
<p>
<table border="0" cellspacing="0" cellpadding="3" class="table_input" style="margin-bottom: 10px; margin-top: 5px; margin-left: 17px;">
<form method="POST" action="index.php">
<input type="hidden" name="uType" value="<?php p($uType); ?>">
<input type="hidden" name="userOffs" value="0">
<tr>
	<td style="padding-left: 10px;"><?php putGS("Full Name"); ?></td>
	<td><input type="text" name="full_name" value="<?php p(htmlspecialchars($userSearchParameters['full_name'])); ?>" class="input_text" style="width: 150px;"></td>
	<td><?php putGS("Account Name"); ?></td>
	<td><input type="text" name="user_name" value="<?php p(htmlspecialchars($userSearchParameters['user_name'])); ?>" class="input_text" style="width: 70px;"></td>
	<td><?php putGS("E-Mail"); ?></td>
	<td><input type="text" name="email" value="<?php p(htmlspecialchars($userSearchParameters['email'])); ?>" class="input_text" style="width: 150px;"></td>
	<td><input type="submit" name="submit_button" value="<?php putGS("Search"); ?>" class="button"></td>
</tr>
<?php if ($uType == "Subscribers") { ?>
<tr>
	<td colspan="11" align="center">
		<?php putGS("Subscription"); ?>&nbsp;
		<select name="subscription_how" class="input_select" style="width: 100px;">
		<?php 
		camp_html_select_option("expires", $userSearchParameters['subscription_how'], getGS("expires")); 
		camp_html_select_option("starts", $userSearchParameters['subscription_how'], getGS("starts")); 
		?>
		</select>
		<select name="subscription_when" class="input_select" style="width: 100px;">
		<?PHP
		camp_html_select_option("before", $userSearchParameters['subscription_when'], getGS("before"));
		camp_html_select_option("after", $userSearchParameters['subscription_when'], getGS("after"));
		camp_html_select_option("on", $userSearchParameters['subscription_when'], getGS("on"));
		?>
		</select>
		<input type="text" name="subscription_date" value="<?php p(htmlspecialchars($userSearchParameters['subscription_date'])); ?>" class="input_text" style="width: 100px;">
		&nbsp;<?php putGS('(yyyy-mm-dd)'); ?>&nbsp;&nbsp;
		<?php putGS("status"); ?>:
		<select name="subscription_status" class="input_select" style="width: 100px;">
		<option value=""></option>
		<?PHP
		camp_html_select_option("active", $userSearchParameters['subscription_status'], getGS("active"));
		camp_html_select_option("inactive", $userSearchParameters['subscription_status'], getGS("inactive"));
		?>
		</select>
	</td>
</tr>
<tr>
	<td colspan="11" align="center">
		<?php putGS("IP address"); ?>:
		<input type="text" class="input_text" name="startIP1" size="3" maxlength="3" value="<?php if ($userSearchParameters['startIP1'] != 0) echo $userSearchParameters['startIP1']; ?>">.
		<input type="text" class="input_text" name="startIP2" size="3" maxlength="3" value="<?php if ($userSearchParameters['startIP2'] != 0) echo $userSearchParameters['startIP2']; ?>">.
		<input type="text" class="input_text" name="startIP3" size="3" maxlength="3" value="<?php if ($userSearchParameters['startIP3'] != 0) echo $userSearchParameters['startIP3']; ?>">.
		<input type="text" class="input_text" name="startIP4" size="3" maxlength="3" value="<?php if ($userSearchParameters['startIP4'] != 0) echo $userSearchParameters['startIP4']; ?>">
		(<?php putGS("fill in from left to right at least one input box"); ?>)
	</td>
</tr>
<?php } // if ($uType == "Subscribers") ?>
</form>
</table>

<?php if ($resMsg != '') { ?>
<table border="0" cellpadding="0" cellspacing="0" class="indent" style="padding-bottom: 5px;">
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
$sqlBase = "SELECT DISTINCT u.* FROM Users AS u";
$sql = '';
if ($userSearchParameters['startIP1'] != 0) {
	$sql .= " LEFT JOIN SubsByIP AS sip ON u.Id = sip.IdUser";
}
if ($userSearchParameters['subscription_date'] != ""
	|| $userSearchParameters['subscription_status'] != "") {
	$sql .= " LEFT JOIN Subscriptions AS s ON u.Id = s.IdUser";
	if ($userSearchParameters['subscription_date'] != "") {
		$sql .= " LEFT JOIN SubsSections AS ss ON s.Id = ss.IdSubscription";
	}
}
$sql .= " WHERE u.Reader = '$isReader'";
if ($userSearchParameters['full_name'] != '') {
	$sql .= " AND Name like '%" . mysql_escape_string($userSearchParameters['full_name']) . "%'";
}
if ($userSearchParameters['user_name'] != '') {
	$sql .= " AND UName like '%" . mysql_escape_string($userSearchParameters['user_name']) . "%'";
}
if ($userSearchParameters['email'] != '') {
	$sql .= " AND EMail like '%" . mysql_escape_string($userSearchParameters['email']) . "%'";
}
if ($userSearchParameters['subscription_date'] != '') {
	$ss_field = "TO_DAYS(ss.StartDate) - TO_DAYS('".$userSearchParameters['subscription_date']."')";
	if ($userSearchParameters['subscription_how'] == 'expires') {
		$ss_field .= " + CAST(Days AS SIGNED)";
	}
	switch ($userSearchParameters['subscription_when']) {
		case 'before': $comp_sign = "<="; break;
		case 'after': $comp_sign = ">="; break;
		case 'on': $comp_sign = "="; break;
	}
	$sql .= " AND ($ss_field) $comp_sign 0";
}
if ($userSearchParameters['subscription_status'] != "") {
	$sql .= " AND s.Active = '" . ($userSearchParameters['subscription_status'] == 'active' ? 'Y' : 'N') . "'";
}
if ($userSearchParameters['startIP1'] != 0) {
	$minIP = $userSearchParameters['startIP1'] * 256 * 256 * 256
		+ $userSearchParameters['startIP2'] * 256 * 256
		+ $userSearchParameters['startIP3'] * 256
		+ $userSearchParameters['startIP4'];
	$maxIP2 = $userSearchParameters['startIP2'] != 0 ? $userSearchParameters['startIP2'] : 255;
	$maxIP3 = $userSearchParameters['startIP3'] != 0 ? $userSearchParameters['startIP3'] : 255;
	$maxIP4 = $userSearchParameters['startIP4'] != 0 ? $userSearchParameters['startIP4'] : 255;
	$maxIP = $userSearchParameters['startIP1'] * 256 * 256 * 256 + $maxIP2 * 256 * 256 + $maxIP3 * 256 + $maxIP4;
	$sql .= " AND ((sip.StartIP >= $minIP AND sip.StartIP <= $maxIP)"
	     . " OR ((sip.StartIP - 1 + sip.Addresses) >= $minIP AND (sip.StartIP - 1 + sip.Addresses) <= $maxIP))";
}
if ($userSearchParameters['subscription_date'] != "") {
	$sql .= " GROUP BY s.Id";
}
$sql .= " ORDER BY " . $orderFields[$orderField] . " $orderDir";
$searchSql = $sqlBase.$sql." LIMIT $userOffs, $lpp";
$res = $Campsite['db']->Execute($searchSql);

$countSql = "SELECT COUNT(*) FROM Users as u ".$sql;
$totalUsers = $Campsite['db']->GetOne($countSql);

$pager =& new SimplePager($totalUsers, $lpp, "userOffs", "index.php?".get_user_urlparams(0)."&", false);

if (gettype($res) == 'object' && $res->NumRows() > 0) {
	$nr = $res->NumRows();
	$last = $nr > $lpp ? $lpp : $nr;
	?>
	<table class="indent">
	<tr>
		<td>
			<?php echo $pager->render(); ?>
		</td>
	</tr>
	</table>
	<table border="0" cellspacing="1" cellpadding="3" class="table_list">
	<tr class="table_list_header">
		<td align="left" valign="center">
			<table><tr>
			<td><b><a href="?<?php echo "$typeParam&" . $orderURLs['fname']; ?>"><?php putGS("Full Name"); ?></a></b></td>
			<td><?php if ($orderField == 'fname') echo $orderSigns['fname']; ?></td>
			</tr></table>
		</td>
		<td align="left" valign="center">
			<table><tr>
			<td><b><a href="?<?php echo "$typeParam&" . $orderURLs['uname']; ?>"><?php putGS("Account Name"); ?></a></b></td>
			<td><?php if ($orderField == 'uname') echo $orderSigns['uname']; ?></td>
			</tr></table>
		</td>
		<td align="left" valign="center"><b><?php putGS("E-Mail"); ?></b></td>
<?php if ($uType == "Subscribers" && $User->hasPermission("ManageSubscriptions")) { ?>
		<td align="left" valign="top"><b><?php putGS("Subscriptions"); ?></b></td>
<?php } ?>
		<td align="left" valign="center">
			<?php putGS("User Type"); ?>
		</td>
		<td align="left" valign="center">
			<table><tr>
			<td><b><a href="?<?php echo "$typeParam&" . $orderURLs['cdate']; ?>"><?php putGS("Creation Date"); ?></a></b></td>
			<td><?php if ($orderField == 'cdate') echo $orderSigns['cdate']; ?></td>
			</tr></table>
		</td>
<?php if ($canDelete) { ?>
		<td align="left" valign="center"><b><?php putGS("Delete"); ?></b></td>
<?php } ?>
	</TR>
<?php 
for($loop = 0; $loop < $last; $loop++) {
	$row = $res->FetchRow();
	$userId = $row['Id'];
	$rowClass = ($loop + 1) % 2 == 0 ? "list_row_even" : "list_row_odd";
	$editUser =& new User($userId);
	$userType = UserType::GetUserTypeFromConfig($editUser->getConfig());
?>
	<tr <?php echo "class=\"$rowClass\""; ?>>
		<td>
		<?php
			if ($canManage) {
				echo "<a href=\"edit.php?" . get_user_urlparams($userId, false, true) . "\">";
			}
			echo htmlspecialchars($row['Name']);
			if ($canManage) {
				echo "</a>";
			}
		?>
		</td>
		<td><?php echo htmlspecialchars($row['UName']); ?></TD>
		<td><?php echo htmlspecialchars($row['EMail']); ?></td>
		<?php if ($uType == "Subscribers" && $User->hasPermission("ManageSubscriptions")) { ?>
		<td><a href="<?php echo "/$ADMIN/users/subscriptions/?f_user_id=$userId"; ?>">
			<?php putGS("Subscriptions"); ?>
		</td>
		<?php } ?>
		<td><?php if ($userType !== false) { echo $userType->getName(); } ?></td>
		<td>
			<?php
				$creationDate = $row['time_created'];
				if ((int)$creationDate == 0) {
					echo "N/A";
				} else {
					echo $creationDate;
				}
			?>
		</td>
<?php
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
?>
</table>
<table class="indent">
<tr>
	<td>
		<?php echo $pager->render(); ?>
	</td>
</tr>
</table>
<?php  } else { ?>
	<blockquote>
	<li><?php  putGS('User list is empty.'); ?></li>
	</blockquote>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
