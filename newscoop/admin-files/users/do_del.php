<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/Log.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canDelete) {
	camp_html_display_error($translator->trans('You do not have the right to delete user accounts.', array(), 'users'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if (!$editUser->exists()) {
	camp_html_display_error($translator->trans('No such user account.', array(), 'users'));
	exit;
}
$uName = $editUser->getUserName();
$editUser->delete();
reset_user_search_parameters();

$typeParam = 'uType=' . urlencode($uType);
camp_html_add_msg($translator->trans('User account $1 was deleted successfully.', array('$1' => $uName), 'users'), "ok");
camp_html_goto_page("/$ADMIN/users/?$typeParam");

?>
