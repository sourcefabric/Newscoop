<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
require_once('permission_list.php');

$uType = 'Staff';
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canManage) {
	$error = getGS("You do not have the right to change user account permissions.");
	camp_html_display_error($error);
	exit;
}

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
	$editUser = new User($userId);
	if ($editUser->getUserName() == '') {
		camp_html_display_error(getGS('No such user account.'));
		exit;
	}
} else {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}

$rights = camp_get_permission_list();
?>
<table border="0" cellspacing="0" cellpadding="3" align="left">
<?php
foreach ($rights as $group_name => $group) {
?>
	<tr>
		<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
			--- <?php p($group_name); ?> ---
		</td>
	</tr>
<?php
	foreach ($group as $right_name => $right_text) {
?>
	<tr>
		<td align="right" style="padding-left: 10px;">
			<input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" <?php if ($editUser->hasPermission($right_name)) { p("CHECKED"); } ?>>
		</td>
		<td style="padding-right: 10px;">
			<?php p($right_text); ?>
		</td>
	</tr>
<?php
	}
}
?>
</table>
