<?php
camp_load_translation_strings("users");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/IPAccess.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');
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

$defaultUserSearchParameters = array('full_name'=>'', 'user_name'=>'', 'email'=>'',
	'subscription_how'=>'expires', 'subscription_when'=>'before', 'subscription_date'=>'',
	'subscription_status'=>'', 'startIP1'=>'', 'startIP2'=>'', 'startIP3'=>'', 'startIP4'=>'');
$userSearchParameters = array('full_name'=>'', 'user_name'=>'', 'email'=>'',
	'subscription_how'=>'expires', 'subscription_when'=>'before', 'subscription_date'=>'',
	'subscription_status'=>'', 'startIP1'=>'', 'startIP2'=>'', 'startIP3'=>'', 'startIP4'=>'');

function read_user_common_parameters()
{
	global $uType, $userOffs, $ItemsPerPage;
	global $defaultUserSearchParameters, $userSearchParameters;

	$uType = Input::Get('uType', 'string', '');
	$userOffs = camp_session_get('userOffs', 0);
	if ($userOffs < 0) {
		$userOffs = 0;
	}
	$ItemsPerPage = Input::Get('ItemsPerPage', 'int', 10);
	foreach ($userSearchParameters as $parameter=>$defaultValue) {
		$userSearchParameters[$parameter] =
			camp_session_get($parameter, $defaultUserSearchParameters[$parameter]);
	}
}

function user_search_is_set()
{
	global $defaultUserSearchParameters, $userSearchParameters;

	foreach ($userSearchParameters as $parameter=>$defaultValue) {
		if ($userSearchParameters[$parameter] != $defaultUserSearchParameters[$parameter]) {
			return true;
		}
	}
	return false;
}

function reset_user_search_parameters()
{
	global $userSearchParameters, $_REQUEST, $_GET, $_POST, $_SESSION;

	foreach ($userSearchParameters as $parameter=>$defaultValue) {
		if (isset($_REQUEST[$parameter])) {
			unset($_REQUEST[$parameter]);
		}
		if (isset($_GET[$parameter])) {
			unset($_GET[$parameter]);
		}
		if (isset($_POST[$parameter])) {
			unset($_POST[$parameter]);
		}
		if (isset($_SESSION[$parameter])) {
			unset($_SESSION[$parameter]);
		}
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

	if ($url != '' && $url[0] == '&')
		$url = substr($url, 1);
	return $url;
}

?>