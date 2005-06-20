<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to change user passwords.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$typeParam = 'uType=' . urlencode($uType);
$password = Input::Get('password', 'string', 0);
$passwordConf = Input::Get('passwordConf', 'string', 0);
$backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getId();

if ($userId == $User->getId()) {
	$oldPassword = Input::Get('oldPassword');
	if (!$editUser->isValidPassword($oldPassword)) {
		CampsiteInterface::DisplayError(getGS('The password you typed is incorrect.'), $backLink);
		exit;
	}
}
if (strlen($password) < 6 || $password != $passwordConf) {
	CampsiteInterface::DisplayError(getGS('The password must be at least 6 characters long and both passwords should match.'), $backLink);
	exit;
}

$editUser->setPassword($password);
$logtext = getGS('Password changed for $1', $editUser->getUserName());
Log::Message($logtext, $editUser->getUserName(), 54);

header("Location: /$ADMIN/users/edit.php?$typeParam&User=" . $editUser->getId());

?>
