<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

list($g_hasAccess, $g_user) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
$uType = 'Subscribers';
compute_user_rights($g_user, $g_canManage, $g_canDelete);
if (!$g_canManage) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to change user account information.'));
	exit;
}

// read input
$g_userId = Input::Get('User', 'int', 0);
$g_editUser = new User($g_userId);
if ($g_editUser->getUserName() == '') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$g_backLink = "/$ADMIN/users/edit.php?uType=Subscribers&User=$g_userId";
$g_cStartIP1 = Input::Get('cStartIP1', 'int', -1);
$g_cStartIP2 = Input::Get('cStartIP2', 'int', -1);
$g_cStartIP3 = Input::Get('cStartIP3', 'int', -1);
$g_cStartIP4 = Input::Get('cStartIP4', 'int', -1);
$g_cAddresses = Input::Get('cAddresses', 'int', 0);

// check if input was correct
if ($g_cStartIP1 == -1 || $g_cStartIP2 == -1 || $g_cStartIP3 == -1 || $g_cStartIP4 == -1) {
	CampsiteInterface::DisplayError(getGS('You must complete the $1 field.', 'Start IP'),
		$g_backLink);
	exit;
}
if ($g_cAddresses == 0) {
	$g_errorMsg = getGS('You must complete the $1 field.', 'Number of addresses');
	CampsiteInterface::DisplayError($g_errorMsg, $g_backLink);
	exit;
}

// check if the IP address group exists already
$g_startIP = $g_cStartIP1*256*256*256+$g_cStartIP2*256*256+$g_cStartIP3*256+$g_cStartIP4;
$g_endIP = $g_startIP + $g_cAddresses - 1;
$g_startIPStr = "$g_cStartIP1.$g_cStartIP2.$g_cStartIP3.$g_cStartIP4";
$g_sql = "SELECT Addresses FROM SubsByIP WHERE "
	. "(StartIP <= $g_startIP AND (StartIP + Addresses - 1) >= $g_startIP) OR "
	. "(StartIP <= $g_endIP AND (StartIP + Addresses - 1) >= $g_endIP)";
$g_res = $Campsite['db']->Execute($g_sql);
if ($g_res->RecordCount() > 0) {
	$g_errorMsg = getGS('The IP address group $1:$2 conflicts with another existing group.',
		$g_startIPStr, $g_cAddresses);
	CampsiteInterface::DisplayError($g_errorMsg, $g_backLink);
	exit;
}

if ($Campsite['db']->Execute("INSERT IGNORE INTO SubsByIP SET IdUser=$g_userId, StartIP='$g_startIP', Addresses=$g_cAddresses")) {
	$logtext = getGS('IP Group $1 added for user $2', encHTML("$g_startIPStr:$g_cAddresses"),
		encHTML($g_editUser->getUserName()));
	Log::Message($logtext, $g_user->getUserName(), 57);
} else {
	CampsiteInterface::DisplayError(getGS('There was an error creating the IP address group.', "$g_startIPStr:$g_cAddresses"), $g_backLink);
	exit;
}

$g_resMsg = getGS("The IP Group $1 has been created.", "$g_startIPStr:$g_cAddresses");
header("Location: $g_backLink&res=OK&resMsg=" . urlencode($g_resMsg));

?>
