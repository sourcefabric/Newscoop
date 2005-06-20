<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
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
$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Readers' ? 'Y' : 'N';

$editUser->setProperty('Name', Input::Get('Name', 'string', ''), false);
$editUser->setProperty('Title', Input::Get('Title', 'string', ''), false);
$editUser->setProperty('Gender', Input::Get('Gender', 'string', ''), false);
$editUser->setProperty('Age', Input::Get('Age', 'string', ''), false);
$editUser->setProperty('EMail', Input::Get('EMail', 'string', ''), false);
$editUser->setProperty('City', Input::Get('City', 'string', ''), false);
$editUser->setProperty('StrAddress', Input::Get('StrAddress', 'string', ''), false);
$editUser->setProperty('State', Input::Get('State', 'string', ''), false);
$editUser->setProperty('CountryCode', Input::Get('CountryCode', 'string', ''), false);
$editUser->setProperty('Phone', Input::Get('Phone', 'string', ''), false);
$editUser->setProperty('Fax', Input::Get('Fax', 'string', ''), false);
$editUser->setProperty('Contact', Input::Get('Contact', 'string', ''), false);
$editUser->setProperty('Phone2', Input::Get('Phone2', 'string', ''), false);
$editUser->setProperty('PostalCode', Input::Get('PostalCode', 'string', ''), false);
$editUser->setProperty('Employer', Input::Get('Employer', 'string', ''), false);
$editUser->setProperty('EmployerType', Input::Get('EmployerType', 'string', ''), false);
$editUser->setProperty('Position', Input::Get('Position', 'string', ''), false);
$editUser->commit();

$logtext = getGS('User account information changed for $1', $editUser->getUserName());
Log::Message($logtext, $editUser->getUserName(), 56);

header("Location: /$ADMIN/users/edit.php?$typeParam&User=" . $editUser->getId());

?>
