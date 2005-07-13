<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canDelete) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to delete user accounts.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$typeParam = 'uType=' . urlencode($uType);
$uName = $editUser->getUserName();

if ($Campsite['db']->Execute("DELETE FROM Users WHERE Id = $userId")) {
	$Campsite['db']->Execute("DELETE FROM UserPerm WHERE IdUser = $userId");
	$res = $Campsite['db']->Execute("SELECT Id FROM Subscriptions WHERE IdUser = $userId");
	while ($row = $res->FetchRow()) {
		$Campsite['db']->Execute("DELETE FROM SubsSections WHERE IdSubscription=".encS($row['Id']));
	}
	$Campsite['db']->Execute("DELETE FROM Subscriptions WHERE IdUser=$userId");
	$Campsite['db']->Execute("DELETE FROM SubsByIP WHERE IdUser=$userId");
	Log::Message(getGS('The user account $1 has been deleted.', $uName), $User->getUserName(), 52);
}

$resMsg = getGS('User account $1 was deleted successfully.', $uName);
header("Location: /$ADMIN/users/?$typeParam&res=OK&resMsg=" . urlencode($resMsg));

?>
