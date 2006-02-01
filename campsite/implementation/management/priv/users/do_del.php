<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($User, $canManage, $canDelete);
if (!$canDelete) {
	camp_html_display_error(getGS('You do not have the right to delete user accounts.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}
$typeParam = 'uType=' . urlencode($uType);
$uName = $editUser->getUserName();
$editUser->delete();

$resMsg = getGS('User account $1 was deleted successfully.', $uName);
header("Location: /$ADMIN/users/?$typeParam&res=OK&resMsg=" . urlencode($resMsg));

?>
