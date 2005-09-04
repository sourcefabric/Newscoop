<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
$uType = 'Subscribers';
compute_user_rights($User, $canManage, $canDelete);
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
$StartIP = Input::Get('StartIP', 'string', '');
$ip0 = ($StartIP & 0xff000000) >> 24;
$ip1 = ($StartIP & 0x00ff0000) >> 16;
$ip2 = ($StartIP & 0x0000ff00) >> 8;
$ip3 = $StartIP & 0x000000ff;
$ip = "$ip0.$ip1.$ip2.$ip3";

if ($Campsite['db']->Execute("DELETE FROM SubsByIP WHERE IdUser=$userId and StartIP=$StartIP")) {
	$logtext = getGS('The IP address group $1 has been deleted.', $ip);
	Log::Message($logtext, $User->getUserName(), 58);
} else {
	header("Location: /$ADMIN/users/edit.php?uType=Subscribers&User=$userId");
	exit;
}

$resMsg = getGS("The IP address group $1 has been deleted.", $ip);
header("Location: /$ADMIN/users/edit.php?uType=Subscribers&User=$userId&res=OK&resMsg=" . urlencode($resMsg));

?>
