<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to delete user types.");
	camp_html_display_error($error);
	exit;
}

$uType = Input::Get('UType', 'string', '');
if (!empty($uType)) {
	$userType = new UserType($uType);
	if (!$userType->exists()) {
		camp_html_display_error(getGS('No such user type.'));
		exit;
	}
	$userType->delete();
} else {
	camp_html_display_error(getGS('No such user type.'));
	exit;
}

header("Location: /$ADMIN/user_types/");

?>