<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
$uType = 'Subscribers';
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canManage) {
	camp_html_display_error(getGS('You do not have the right to change user account information.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}
$startIP = Input::Get('StartIP', 'string', '');
$ipAccess = new IPAccess($userId, $startIP);
$startIPstring = $ipAccess->getStartIPstring();
$addresses = $ipAccess->getAddresses();

if (!$ipAccess->delete()) {
	camp_html_goto_page("/$ADMIN/users/edit.php?uType=Subscribers&User=$userId");
}

camp_html_add_msg(getGS("The IP address group $1 has been deleted.", "$startIPstring:$addresses"), "ok");
camp_html_goto_page("/$ADMIN/users/edit.php?uType=Subscribers&User=$userId");

?>