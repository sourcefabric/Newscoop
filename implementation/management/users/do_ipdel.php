<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
$uType = 'Readers';
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to change user account information.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$StartIP = Input::Get('StartIP', 'string', '');

query ("SELECT Addresses FROM SubsByIP WHERE IdUser=$userId and StartIP=$StartIP", 'ig');
if ($NUM_ROWS) {
	fetchRow($ig);
	query("DELETE FROM SubsByIP WHERE IdUser=$userId and StartIP=$StartIP");
	$logtext = getGS('The IP address group $1 has been deleted.',
		"$StartIP:" . getHVar($ig, 'Addresses'));
	Log::Message($logtext, $editUser->getUserName(), 58);
}

header("Location: /$ADMIN/users/edit.php?uType=Readers&User=$userId");

?>
