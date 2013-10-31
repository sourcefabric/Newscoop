<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/Log.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
$uType = 'Subscribers';
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canManage) {
	camp_html_display_error($translator->trans('You do not have the right to change user account information.', array(), 'users'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	camp_html_display_error($translator->trans('No such user account.', array(), 'users'));
	exit;
}
$startIP = Input::Get('StartIP', 'string', '');
$ipAccess = new IPAccess($userId, $startIP);
$startIPstring = $ipAccess->getStartIPstring();
$addresses = $ipAccess->getAddresses();

if (!$ipAccess->delete()) {
	camp_html_goto_page("/$ADMIN/users/edit.php?uType=Subscribers&User=$userId");
}

camp_html_add_msg($translator->trans("The IP address group $1 has been deleted.", array('$1' => "$startIPstring:$addresses"), 'users'), "ok");
camp_html_goto_page("/$ADMIN/users/edit.php?uType=Subscribers&User=$userId");

?>