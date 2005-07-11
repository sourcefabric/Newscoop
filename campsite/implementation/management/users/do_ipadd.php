<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
$uType = 'Subscribers';
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to change user account information.'));
	exit;
}

// read input
$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$backLink = "/$ADMIN/users/edit.php?uType=Subscribers&User=$userId";
$cStartIP1 = Input::Get('cStartIP1', 'int', 0);
$cStartIP2 = Input::Get('cStartIP2', 'int', 0);
$cStartIP3 = Input::Get('cStartIP3', 'int', 0);
$cStartIP4 = Input::Get('cStartIP4', 'int', 0);
$cAddresses = Input::Get('cAddresses', 'int', 0);

// check if input was correct
if ($cStartIP1 == 0 || $cStartIP2 == 0 || $cStartIP3 == 0 || $cStartIP4 == 0) {
	CampsiteInterface::DisplayError(getGS('You must complete the $1 field.', 'Start IP'),
		$backLink);
	exit;
}
if ($cAddresses == 0) {
	CampsiteInterface::DisplayError(getGS('You must complete the $1 field.',
			'Number of addresses'), $backLink);
	exit;
}

// check if the IP address group exists already
$StartIP = $cStartIP1*256*256*256+$cStartIP2*256*256+$cStartIP3*256+$cStartIP4;
$ip = "$cStartIP1.$cStartIP2.$cStartIP3.$cStartIP4";
query ("SELECT Addresses FROM SubsByIP WHERE IdUser=$userId and StartIP=$StartIP", 'ig');
if ($NUM_ROWS) {
	CampsiteInterface::DisplayError(getGS('An IP address group having the $1 start address already exists.', $ip), $backLink);
	exit;
}

if ($Campsite['db']->Execute("INSERT IGNORE INTO SubsByIP SET IdUser=$userId, StartIP='$StartIP', Addresses=$cAddresses")) {
	$logtext = getGS('IP Group $1 added for user $2', encHTML("$ip:$cAddresses"),
		encHTML($editUser->getUserName()));
	Log::Message($logtext, $User->getUserName(), 57);
} else {
	CampsiteInterface::DisplayError(getGS('There was an error creating the IP address group.', "$ip:$cAddresses"), $backLink);
	exit;
}

$resMsg = getGS("The IP Group $1 has been created.", "$ip:$cAddresses");
header("Location: $backLink&res=OK&resMsg=" . urlencode($resMsg));

?>
