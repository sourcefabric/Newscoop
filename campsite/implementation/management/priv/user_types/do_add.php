<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");

list($access, $User) = check_basic_access($_REQUEST);
$canManage = $User->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$uType = Input::Get('Name', 'string', '');
if ($uType != '') {
	$userType = new UserType($uType);
	if ($userType->exists()) {
		$errMsg = getGS("An user type with the name '$1' already exists.", $uType);
		camp_html_display_error($errMsg);
		exit;
	}
} else {
	camp_html_display_error(getGS('You must complete the $1 field.', getGS('Name')));
	exit;
}

$rightsFields = User::GetDefaultConfig();
foreach ($rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	if ($val == 'on') {
		$rightsFields[$field] = 'Y';
	}
}
if ($userType->create($uType, $rightsFields)) {
	$logtext = getGS('User type $1 added', $uType);
	Log::Message($logtext, $uType, 121);
}

header("Location: /$ADMIN/user_types/access.php?UType=$uType");

?>
