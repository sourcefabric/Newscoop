<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/user_types/utypes_common.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$uTypeId = Input::Get('UType', 'string', '');
if (is_numeric($uTypeId) && $uTypeId > 0) {
	$userType = new UserType($uTypeId);
	if ($userType->getName() == '') {
		camp_html_display_error(getGS('No such user type.'));
		exit;
	}
} else {
	camp_html_display_error(getGS('No such user type.'));
	exit;
}

$rightsFields = User::GetDefaultConfig();
foreach ($rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	$userType->setPermission($field, ($val != 'off'));
}
$logtext = getGS('Permissions changed for user type "$1"', $userType->getName());
Log::Message($logtext, $userType->getName(), 123);

$msg = getGS("Permissions successfully modified");
camp_html_add_msg($msg, 'ok');
camp_html_goto_page("/$ADMIN/user_types/access.php?UType=$uTypeId");

?>