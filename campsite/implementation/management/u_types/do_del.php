<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/u_types/utypes_common.php");

list($access, $User) = check_basic_access($_REQUEST);
$canManage = $User->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to delete user types.");
	CampsiteInterface::DisplayError($error);
	exit;
}

$uType = Input::Get('UType', 'string', '');
if ($uType != '') {
	$userType = new UserType($uType);
	if ($userType->getName() == '') {
		CampsiteInterface::DisplayError(getGS('No such user type.'));
		exit;
	}
} else {
	CampsiteInterface::DisplayError(getGS('No such user type.'));
	exit;
}

$query = "DELETE FROM UserTypes WHERE Name = '$uType'";
if ($Campsite['db']->Execute($query)) {
	$logtext = getGS('User type $1 deleted', $uType);
	Log::Message($logtext, $uType, 122);
}

header("Location: /$ADMIN/u_types/");

?>
