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

// define fields arrays
$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
$notNullFields = array('UName'=>'User name', 'Name'=>'Full Name', 'EMail'=>'E-Mail',
	'Gender'=>'Gender', 'CountryCode'=>'Country', 'Type'=>'Type');

// read fields values
$errorField = "";
$typeParam = 'uType=' . urlencode($uType);
$backLink = "/$ADMIN/users/edit.php?$typeParam";
foreach ($fields as $index=>$field) {
	$fieldValues[$field] = Input::Get($field, 'string', '');
	if ($fieldValues[$field] != '')
		$backLink .= "&" . urlencode($field) . "=" . urlencode($fieldValues[$field]);
	elseif (array_key_exists($field, $notNullFields) && $errorField == "")
		$errorField = $field;
}

// set the Reader field
$Type = Input::Get('Type', 'string', '');
$fieldValues['Reader'] = $uType == 'Readers' ? 'Y' : 'N';
if ($uType == 'Staff' && $Type == '' && $errorField == '')
	$errorField = 'Type';

// display errors if found
if ($errorField != "") {
	$desc = $notNullFields[$errorField];
	if ($errorField == 'CountryCode' || $errorField == 'Gender' || $errorField == 'Type') {
		$errorMsg = getGS('You must select a $1', $desc);
	}
	else {
		$errorMsg = getGS('You must complete the $1 field.', $desc);
	}
	CampsiteInterface::DisplayError($errorMsg, $backLink);
	exit;
}

// read password
$password = Input::Get('password', 'string', '');
$passwordConf = Input::Get('passwordConf', 'string', '');
if (strlen($password) < 6 || $password != $passwordConf) {
	CampsiteInterface::DisplayError(getGS('The password must be at least 6 characters long and both passwords should match.'), $backLink);
	exit;
}

// create user
$editUser = new User;
if ($editUser->create($fieldValues)) {
	$editUser->setPassword($password);
	if ($uType == 'Staff')
		$editUser->setUserType($Type);
	$logtext = getGS('User account $1 created', $editUser->getUserName());
	Log::Message($logtext, $editUser->getUserName(), 51);
	header("Location: /$ADMIN/users/edit.php?$typeParam&User=" . $editUser->getId());
} else {
	CampsiteInterface::DisplayError(getGS('The user account could not be created.'), $backLink);
	exit;
}

?>
