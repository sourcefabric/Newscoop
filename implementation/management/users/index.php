<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, &$canManage, &$canDelete);

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Subscribers' ? 'Y' : 'N';

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR><TD class="page_title" align="left"><?php putGS("$uType management"); ?></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
<?php
if ($canManage) {
	$addLink = "edit.php?" . get_user_urlparams(0, true, true);
?>
	<TD><A HREF="<?php echo $addLink; ?>">
		<IMG SRC="/admin/img/icon/add.png" BORDER="0">
		<B><?php putGS("Add new user account"); ?></B></A></TD>
<?php } ?>
	<TD style="padding-left: 10px;">
		<A HREF="?<?php echo get_user_urlparams(0, false, true); ?>">
		<IMG SRC="/admin/img/icon/reset.png" BORDER="0">
		<B><?php putGS("Reset search conditions"); ?></B></A></TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input" style="margin-bottom: 10px; margin-top: 5px;" align="center">
<form method="POST" action="index.php">
<input type="hidden" name="uType" value="<?php pencHTML($uType); ?>">
<input type="hidden" name="userOffs" value="<?php pencHTML($userOffs); ?>">
<input type="hidden" name="lpp" value="<?php pencHTML($lpp); ?>">
<tr>
	<td style="padding-left: 10px;"><?php putGS("Full Name"); ?></td>
	<td><input type="text" name="full_name" value="<?php pencHTML($full_name); ?>" class="input_text" style="width: 150px;"></td>
	<td><?php putGS("User Name"); ?></td>
	<td><input type="text" name="user_name" value="<?php pencHTML($user_name); ?>" class="input_text" style="width: 70px;"></td>
	<td><?php putGS("E-Mail"); ?></td>
	<td><input type="text" name="email" value="<?php pencHTML($email); ?>" class="input_text" style="width: 150px;"></td>
	<td><input type="submit" name="submit_button" value="<?php putGS("Search"); ?>" class="button"></td>
</tr>
<?php if ($uType == "Subscribers") { ?>
<tr>
	<td colspan="11" align="center">
		<?php putGS("Subscription"); ?>&nbsp;
		<select name="subscription_how" class="input_select" style="width: 100px;">
			<option value="expires" <?php printSelected($subscription_how, 'expires'); ?>><?php putGS("expires"); ?></option>
			<option value="starts" <?php printSelected($subscription_how, 'starts'); ?>><?php putGS("starts"); ?></option>
		</select>
		<select name="subscription_when" class="input_select" style="width: 100px;">
			<option value="before" <?php printSelected($subscription_when, 'before'); ?>><?php putGS("before"); ?></option>
			<option value="after" <?php printSelected($subscription_when, 'after'); ?>><?php putGS("after"); ?></option>
			<option value="on" <?php printSelected($subscription_when, 'on'); ?>><?php putGS("on"); ?></option>
		</select>
		<input type="text" name="subscription_date" value="<?php pencHTML($subscription_date); ?>" class="input_text" style="width: 100px;">
		&nbsp;<?php putGS('(yyyy-mm-dd)'); ?>&nbsp;&nbsp;
		<?php putGS("status"); ?>:
		<select name="subscription_status" class="input_select" style="width: 100px;">
			<option value=""></option>
			<option value="active" <?php printSelected($subscription_status, 'active'); ?>><?php putGS("active"); ?></option>
			<option value="inactive" <?php printSelected($subscription_status, 'inactive'); ?>><?php putGS("inactive"); ?></option>
		</select>
	</td>
</tr>
<?php } ?>
</form>
</TABLE>

<?php
$sql = "SELECT u.* FROM Users as u";
if ($subscription_date != "" || $subscription_status != "") {
	$sql .= " left join Subscriptions as s on u.Id = s.IdUser";
	if ($subscription_date != "")
		$sql .= " left join SubsSections as ss on s.Id = ss.IdSubscription";
}
$sql .= " where u.Reader = '$isReader'";
if ($full_name != '')
	$sql .= " and Name like '%" . mysql_escape_string($full_name) . "%'";
if ($user_name != '')
	$sql .= " and UName like '%" . mysql_escape_string($user_name) . "%'";
if ($email != '')
	$sql .= " and EMail like '%" . mysql_escape_string($email) . "%'";
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
	$sql .= " and ($ss_field) $comp_sign 0";
}
if ($subscription_status != "")
	$sql .= " and s.Active = '" . ($subscription_status == 'active' ? 'Y' : 'N') . "'";
if ($subscription_date != "")
	$sql .= " group by s.Id";
$sql .= " order by Name asc limit $userOffs, " . ($lpp + 1);
//echo "<p>sql: $sql</p>\n";
query($sql, 'Users');
if ($NUM_ROWS) {
	$nr = $NUM_ROWS;
	$last = $nr > $lpp ? $lpp : $nr;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Full Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("User Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("E-Mail"); ?></B></TD>
<?php if ($uType == "Subscribers" && $User->hasPermission("ManageSubscriptions")) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Subscriptions"); ?></B></TD>
<?php } ?>
<?php if ($canDelete) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%"><B><?php putGS("Delete"); ?></B></TD>
<?php } ?>
	</TR>
<?php 
for($loop = 0; $loop < $last; $loop++) {
	fetchRow($Users);
	$userId = getVar($Users, 'Id');
	$rowClass = ($loop + 1) % 2 == 0 ? "list_row_even" : "list_row_odd";
?>
	<tr <?php echo "class=\"$rowClass\""; ?>>
		<td>
<?php
	if ($canManage)
		echo "<a href=\"edit.php?" . get_user_urlparams($userId, false, true) . "\">";
	pgetHVar($Users,'Name');
	if ($canManage)
		echo "</a>";
	$old_user_name = $user_name;
	$user_name = getVar($Users, 'UName');
?>
		</td>
		<td><?php pgetHVar($Users,'UName'); ?></TD>
<?php
	$user_name = $old_user_name;
	$old_email = $email;
	$email = getVar($Users, 'EMail');
?>
		<td><?php pgetHVar($Users,'EMail'); ?></td>
<?php if ($uType == "Subscribers" && $User->hasPermission("ManageSubscriptions")) { ?>
		<td><a href="<?php echo "/$ADMIN/users/subscriptions/?User=$userId"; ?>">
			<?php putGS("Subscriptions"); ?></td>
<?php } ?>
<?php
	$email = $old_email;
	if ($canDelete) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/admin/users/do_del.php?<?php echo get_user_urlparams($userId, false, true); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the user account $1 ?', getVar($Users, 'UName')); ?>');">
				<IMG SRC="/admin/img/icon/delete.png" BORDER="0" ALT="<?php putGS('Delete user $1',getHVar($Users,'Name')); ?>" TITLE="<?php  putGS('Delete user $1',getHVar($Users,'Name')); ?>">
			</A>
		</TD>
<?php
	}
?>
	</TR>
<?php 
}
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php
if ($userOffs <= 0) {
	echo "\t\t&lt;&lt;&nbsp;" . getGS('Previous');
} else {
	$oldUserOffs = $userOffs;
	$userOffs = $userOffs - $lpp;
?>		<B><A HREF="index.php?<?php echo get_user_urlparams(0); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php
	$userOffs = $oldUserOffs;
}
if ($nr < $lpp+1) {
	echo "\t\t| " . getGS('Next') . "&nbsp;&gt;&gt;";
} else {
	$userOffs += $lpp;
?>		 | <B><A HREF="index.php?<?php echo get_user_urlparams(0); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('User list is empty.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>

</HTML>
