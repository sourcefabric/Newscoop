<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$uType = Input::Get('Name', 'string', '');
if ($uType != '') {
	$userType = new UserType($uType);
	if ($userType->exists()) {
		$errMsg = getGS("A user type with the name '$1' already exists.", $uType);
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
$userType->create($uType, $rightsFields);

camp_html_goto_page("/$ADMIN/user_types/access.php?UType=$uType");

?>