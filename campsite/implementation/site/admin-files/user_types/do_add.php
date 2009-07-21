<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/user_types/utypes_common.php");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$BackLink = "add.php";
$uType = Input::Get('Name', 'string', '');
if ($uType != '') {
	$userType = UserType::GetByName($uType);
	if ($userType->exists()) {
		$errMsg = getGS("A user type with the name '$1' already exists.", $uType);
		camp_html_add_msg($errMsg);
        camp_html_goto_page($BackLink);
		exit;
	}
} else {
	camp_html_add_msg(getGS('You must fill in the $1 field.', getGS('Name')));
    camp_html_goto_page($BackLink);
	exit;
}

$rights = array();
$rightsFields = User::GetDefaultConfig();
foreach ($rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	if ($val != 'off') {
		$rights[$field] = 1;
	}
}
$userType->create($uType, $rights);

camp_html_goto_page("/$ADMIN/user_types/access.php?UType=".$userType->getId());

?>