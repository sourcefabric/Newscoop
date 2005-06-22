<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, &$canManage, &$canDelete);

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Subscribers' ? 'Y' : 'N';

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
	$editUser = new User($userId);
	if ($editUser->getUserName() == '') {
		CampsiteInterface::DisplayError(getGS('No such user account.'));
		exit;
	}
	$title = "Change user account information";
} else {
	$editUser = new User();
	$title = "Add new user account";
}

?>
<table border="0" cellspacing="0" cellpadding="1" width="100%" class="page_title_container">
<tr>
	<td class="page_title"><?php putGS($title); ?></td>
	<td align="right"><a href="/<?php echo $ADMIN; ?>/users/?<?php echo get_user_urlparams(); ?>" class="breadcrumb" ><?php putGS($uType);  ?></a></td>
</tr>
</table>
<table border="0" align="center">
<tr>
	<td rowspan="3" valign="top" align="center" height="1%">
		<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/info.php"); ?>
	</td>
<?php if ($userId > 0) { ?>
	<td valign="top" height="1%">
		<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/passwd.php"); ?>
	</td>
</tr>
<tr>
	<td valign="top" height="1%">
<?php
if ($uType == 'Staff')
	require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/rights.php");
if ($uType == 'Subscribers')
	require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/subscriptions.php");
?>
	</td>
</tr>
<tr>
	<td valign="top">
<?php if ($uType == 'Subscribers') { ?>
	<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/ipaccesslist.php"); ?>
<?php } ?>
	</td>
<?php } ?>
</tr>
</table>
<?php CampsiteInterface::CopyrightNotice(); ?>
