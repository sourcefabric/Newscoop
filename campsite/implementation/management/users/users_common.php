<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("$ADMIN_DIR/users");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");

function verify_user_type()
{
	global $uType;

	if (!isset($uType))
		read_user_common_parameters();

	if ($uType != "Staff" && $uType != "Subscribers") {
		CampsiteInterface::DisplayError(getGS('Invalid value of user type parameter'));
		exit(0);
	}
	return $uType;
}

function read_user_common_parameters()
{
	global $uType, $userOffs, $lpp, $full_name, $user_name, $email, $subscription_how;
	global $subscription_when, $subscription_date, $subscription_status;

	$uType = Input::Get('uType', 'string', '');
	$userOffs = Input::Get('userOffs', 'int', 0);
	if ($userOffs < 0)
		$userOffs = 0;
	$lpp = Input::Get('lpp', 'int', 20);
	$full_name = Input::Get('full_name', 'string', '');
	$user_name = Input::Get('user_name', 'string', '');
	$email = Input::Get('email', 'string', '');
	if ($uType == "Subscribers") {
		$subscription_how = Input::Get('subscription_how', 'string', '');
		$subscription_when = Input::Get('subscription_when', 'string', '');
		$subscription_date = Input::Get('subscription_date', 'string', '');
		$subscription_status = Input::Get('subscription_status', 'string', '');
	}
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