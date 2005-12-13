<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("users");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/IPAccess.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

function verify_user_type()
{
	global $uType;

	if (!isset($uType))
		read_user_common_parameters();

	if ($uType != "Staff" && $uType != "Subscribers") {
		camp_html_display_error(getGS('Invalid value of user type parameter'));
		exit(0);
	}
	return $uType;
}

function read_user_common_parameters()
{
	global $uType, $userOffs, $lpp, $full_name, $user_name, $email, $subscription_how;
	global $subscription_when, $subscription_date, $subscription_status, $res, $resMsg;
	global $startIP1, $startIP2, $startIP3, $startIP4;

	$uType = Input::Get('uType', 'string', '');
	$userOffs = camp_session_get('userOffs', 0);
	if ($userOffs < 0) {
		$userOffs = 0;
	}
	$lpp = Input::Get('lpp', 'int', 2);
	$full_name = camp_session_get('full_name', '');
	$user_name = camp_session_get('user_name', '');
	$email = camp_session_get('email', '');
	if ($uType == "Subscribers") {
		$subscription_how = camp_session_get('subscription_how', '');
		$subscription_when = camp_session_get('subscription_when', '');
		$subscription_date = camp_session_get('subscription_date', '');
		$subscription_status = camp_session_get('subscription_status', '');
	}
	$res = Input::Get('res', 'string', 'OK');
	$resMsg = Input::Get('resMsg', 'string', '');
	$startIP1 = camp_session_get('StartIP1', 0);
	$startIP2 = camp_session_get('StartIP2', 0);
	$startIP3 = camp_session_get('StartIP3', 0);
	$startIP4 = camp_session_get('StartIP4', 0);
}

function compute_user_rights($User, &$canManage, &$canDelete)
{
	global $uType;

	if (!isset($uType))
		read_user_common_parameters();

	switch ($uType) {
	case 'Staff':
		$canManage = $User->hasPermission('ManageUsers');
		$canDelete = $User->hasPermission('DeleteUsers');
		break;
	case 'Subscribers':
		$canManage = $User->hasPermission('ManageReaders');
		$canDelete = $User->hasPermission('ManageReaders');
		break;
	default:
		$canManage = false;
		$canDelete = false;
	}
}

function get_user_urlparams($userId = 0, $print_back_link = false, $strip_search = false)
{
	global $uType, $userOffs, $full_name, $user_name, $email, $subscription_how;
	global $subscription_when, $subscription_date, $subscription_status;

	$params_search = array('uType', 'userOffs', 'full_name', 'user_name', 'email',
		'subscription_how', 'subscription_when', 'subscription_date',
		'subscription_status');
	$params_nosearch = array('uType', 'userOffs');
	if ($strip_search)
		$params = & $params_nosearch;
	else
		$params = & $params_search;

	$url = '';
	if ($userId > 0)
		$url = 'User=' . $userId;
	foreach ($params as $index=>$param) {
		if ($$param != '' && $$param != '0')
			$url .= '&' . urlencode($param) . '=' . urlencode($$param);
	}

	if ($print_back_link) {
		$url .= '&backLink=' . urlencode($_SERVER['REQUEST_URI']);
	}

	return $url;
}

?>