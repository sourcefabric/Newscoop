<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, $canManage, $canDelete);

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Subscribers' ? 'Y' : 'N';

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
	$editUser = new User($userId);
	if ($editUser->getUserName() == '') {
		camp_html_display_error(getGS('No such user account.'));
		exit;
	}
	$isNewUser = false;
} else {
	$editUser = new User();
	$isNewUser = true;
}

?>
<head>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
</head>

<table border="0" cellspacing="0" cellpadding="1" width="100%" class="page_title_container">
<tr>
	<td class="page_title"><?php if ($userId > 0) { putGS("Change user account information"); } else { putGS("Add new user account"); } ?></td>
	<td align="right"><a href="/<?php echo $ADMIN; ?>/users/?<?php echo get_user_urlparams(); ?>" class="breadcrumb" ><?php putGS($uType);  ?></a></td>
</tr>
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

<table border="0">
<tr>
	<td rowspan="2" valign="top" height="1%">
		<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/info.php"); ?>
	</td>
<?php if ($userId > 0) { ?>
	<td valign="top" height="1%">
        <?php
        if (($uType == 'Subscribers') && ($User->hasPermission("ManageSubscriptions"))) {
        	require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/subscriptions.php");
        }
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
<?php camp_html_copyright_notice(); ?>
