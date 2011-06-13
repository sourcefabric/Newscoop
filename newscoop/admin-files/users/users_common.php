<?php
camp_load_translation_strings("users");
require_once($GLOBALS['g_campsiteDir']. '/classes/IPAccess.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Input.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/UserType.php');
require_once($GLOBALS['g_campsiteDir']. '/db_connect.php');

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
	$ItemsPerPage = Input::Get('ItemsPerPage', 'int', 20);
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

	$params = $userSearchParameters;
	$params['userOffs'] = null;

	foreach ($params as $parameter=>$defaultValue) {
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

function get_user_urlparams($userId = 0, $print_back_link = false,
$strip_search = false, $strip_offset = false)
{
	global $uType, $userOffs, $userSearchParameters;

	$params_search = array('full_name', 'user_name', 'email',
		'subscription_how', 'subscription_when', 'subscription_date',
		'subscription_status');
	$params = array('uType');
	if (!$strip_offset) {
		$params[] = 'userOffs';
	}

	$url = '';
	if ($userId > 0)
		$url = 'User=' . $userId;
	foreach ($params as $index=>$param) {
		if ($$param != '' && $$param != '0') {
			$url .= '&' . urlencode($param) . '=' . urlencode($$param);
		}
	}

	if ($print_back_link) {
		$url .= '&backLink=' . urlencode($_SERVER['REQUEST_URI']);
	}

	if (!$strip_search) {
		foreach ($params_search as $param) {
			if (!empty($userSearchParameters[$param])) {
				$url .= '&' . urlencode($param) . '=' . urlencode($userSearchParameters[$param]);
			}
		}
	}

	if ($url != '' && $url[0] == '&')
		$url = substr($url, 1);
	return $url;
}

function get_users_from_search($isReader, $orderFields, $orderField, $orderDir, &$totalUsers)
{
    global $g_ado_db;
    global $userSearchParameters, $userOffs, $ItemsPerPage;

    $sqlBase = "SELECT DISTINCT u.Id, u.Name, u.UName, u.EMail, DATE_FORMAT(u.time_created, '%Y-%m-%d %T') as time_created FROM liveuser_users AS u";
    $sql = '';
    if ($userSearchParameters['startIP1'] != 0) {
        $sql .= " LEFT JOIN SubsByIP AS sip ON u.Id = sip.IdUser";
    }
    if ($userSearchParameters['subscription_date'] != ""
	  || $userSearchParameters['subscription_status'] != "") {
        $sql .= " LEFT JOIN Subscriptions AS s ON u.Id = s.IdUser";
	if ($userSearchParameters['subscription_date'] != "") {
	    $sql .= " LEFT JOIN SubsSections AS ss ON s.Id = ss.IdSubscription";
	}
    }
    $sql .= " WHERE u.Reader = '$isReader'";
    if ($userSearchParameters['full_name'] != '') {
        $sql .= " AND Name like '%" . $g_ado_db->escape($userSearchParameters['full_name']) . "%'";
    }
    if ($userSearchParameters['user_name'] != '') {
        $sql .= " AND UName like '%" . $g_ado_db->escape($userSearchParameters['user_name']) . "%'";
    }
    if ($userSearchParameters['email'] != '') {
        $sql .= " AND EMail like '%" . $g_ado_db->escape($userSearchParameters['email']) . "%'";
    }
    if ($userSearchParameters['subscription_date'] != '') {
        $ss_field = "TO_DAYS(ss.StartDate) - TO_DAYS('".$userSearchParameters['subscription_date']."')";
	if ($userSearchParameters['subscription_how'] == 'expires') {
	    $ss_field .= " + CAST(Days AS SIGNED)";
	}
	switch ($userSearchParameters['subscription_when']) {
	    case 'before': $comp_sign = "<="; break;
	    case 'after': $comp_sign = ">="; break;
	    case 'on': $comp_sign = "="; break;
	}
	$sql .= " AND ($ss_field) $comp_sign 0";
    }
    if ($userSearchParameters['subscription_status'] != "") {
        $sql .= " AND s.Active = '" . ($userSearchParameters['subscription_status'] == 'active' ? 'Y' : 'N') . "'";
    }
    if ($userSearchParameters['startIP1'] != 0) {
        $minIP = $userSearchParameters['startIP1'] * 256 * 256 * 256
	    + $userSearchParameters['startIP2'] * 256 * 256
	    + $userSearchParameters['startIP3'] * 256
	    + $userSearchParameters['startIP4'];
	$maxIP2 = $userSearchParameters['startIP2'] != 0 ? $userSearchParameters['startIP2'] : 255;
	$maxIP3 = $userSearchParameters['startIP3'] != 0 ? $userSearchParameters['startIP3'] : 255;
	$maxIP4 = $userSearchParameters['startIP4'] != 0 ? $userSearchParameters['startIP4'] : 255;
	$maxIP = $userSearchParameters['startIP1'] * 256 * 256 * 256 + $maxIP2 * 256 * 256 + $maxIP3 * 256 + $maxIP4;
	$sql .= " AND ((sip.StartIP >= $minIP AND sip.StartIP <= $maxIP)"
	    . " OR ((sip.StartIP - 1 + sip.Addresses) >= $minIP AND (sip.StartIP - 1 + sip.Addresses) <= $maxIP))";
    }
    if ($userSearchParameters['subscription_date'] != "") {
        $sql .= " GROUP BY s.Id";
    }
    $sql .= " ORDER BY " . $orderFields[$orderField] . " $orderDir";
    $searchSql = $sqlBase.$sql." LIMIT $userOffs, $ItemsPerPage";
    $users = $g_ado_db->GetAll($searchSql);

    $countSql = "SELECT COUNT(*) FROM liveuser_users as u ".$sql;
    $totalUsers = $g_ado_db->GetOne($countSql);

    return $users;
}

?>
