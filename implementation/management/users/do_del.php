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

query("DELETE FROM Users WHERE Id=$userId");
if ($AFFECTED_ROWS > 0) {
	query("DELETE FROM UserPerm WHERE IdUser=$userId");
	query("SELECT Id FROM Subscriptions WHERE IdUser=$userId", 's');
	$nr=$NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		fetchRowNum($s);
		query ("DELETE FROM SubsSections WHERE IdSubscription=".encS(getNumVar($s,0)) );
	}

	query ("DELETE FROM Subscriptions WHERE IdUser=$userId");
	query ("DELETE FROM SubsByIP WHERE IdUser=$userId");
	$logtext = getGS('The user account $1 has been deleted.', $editUser->getUserName());
	Log::Message($logtext, $editUser->getUserName(), 52);
}

header("Location: /$ADMIN/users/?$typeParam");

?>
