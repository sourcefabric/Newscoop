<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/Log.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/UserType.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($g_user, $canManage, $canDelete);

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	camp_html_display_error($translator->trans('No such user account.', array(), 'users'), "/$ADMIN/users/?".get_user_urlparams());
	exit;
}

if (!$canManage && $editUser->getUserId() != $g_user->getUserId()) {
	$errMsg = $translator->trans('You do not have the right to change user account information.', array(), 'users');
	camp_html_display_error($errMsg);
	exit;
}

$typeParam = 'uType=' . urlencode($uType);
$userEmail = Input::Get('EMail', 'string', 0);
if ($userEmail != $editUser->getEmail()) {
    if (User::EmailExists($userEmail, $editUser->getUserName())) {
        $backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId();
        $errMsg = $translator->trans('Another user is registered with that e-mail address, please choose a different one.', array(), 'users');
        camp_html_add_msg($errMsg);
        camp_html_goto_page($backLink);
    }
}

$setPassword = Input::Get('setPassword', 'string', 'false') == 'true';
$customizeRights = Input::Get('customizeRights', 'string', 'false') == 'true';

if ($setPassword) {
	$password = Input::Get('password', 'string', 0);
	$passwordConf = Input::Get('passwordConf', 'string', 0);
	$backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId();

	if ($userId == $g_user->getUserId()) {
		$oldPassword = Input::Get('oldPassword');
		if (!$editUser->isValidPassword($oldPassword)
				&& !$editUser->isValidOldPassword($oldPassword)) {
			camp_html_add_msg($translator->trans('The password you typed is incorrect.', array(), 'users'));
			camp_html_goto_page($backLink);
		}
	}
	if (strlen($password) < 6 || $password != $passwordConf) {
		camp_html_add_msg($translator->trans('The password must be at least 6 characters long and both passwords should match.', array(), 'users'));
		camp_html_goto_page($backLink);
	}

	$editUser->setPassword($password);
    $liveUserValues['passwd'] = $password;
}

$userData = array(
                  'Name', 'Title', 'Gender', 'Age', 'EMail', 'City',
                  'StrAddress', 'State', 'CountryCode', 'Phone', 'Fax',
                  'Contact', 'Phone2', 'PostalCode', 'Employer',
                  'EmployerType', 'Position'
                  );

// save user data
foreach ($userData as $value) {
    $liveUserValues[$value] = Input::Get($value, 'string', null);
    $editUser->setProperty($value, $liveUserValues[$value], false);
}

$backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId();
if ($LiveUserAdmin->updateUser($liveUserValues, $editUser->getPermUserId()) === false) {
    camp_html_add_msg($translator->trans('There was an error when trying to update the user info.', array(), 'users'));
    camp_html_goto_page($backLink);
} else {
    $editUser->commit();
}

if ($editUser->isAdmin() && $customizeRights && $canManage) {
	$rightsFields = $editUser->GetDefaultConfig();
	$permissions = array();
	foreach ($rightsFields as $field=>$value) {
		$val = Input::Get($field, 'string', 'off');
		$permissionEnabled = ($val == 'off') ? false : true;
		$permissions[$field] = $permissionEnabled;
	}
}

if ($editUser->isAdmin() && $customizeRights && $canManage) {
	// save user customized rights
	$editUser->updatePermissions($permissions);
}
if ($editUser->isAdmin() && !$customizeRights && $canManage) {
	// save user rights based on existing user type
	$userTypeId = Input::Get('UserType', 'int', 0);
	if ($userTypeId != 0) {
		$editUser->setUserType($userTypeId);
	}
}

// unsubscribe
$unsubscribe = Input::Get('unsubscribe', 'bool', false);
if ($unsubscribe
    && ($canManage || $editUser->getUserId() == $g_user->getUserId())) {
    $editUser->setPermission('MailNotify', false);
}

camp_html_add_msg($translator->trans("User $1 information was changed successfully.", array(
	'$1' => $editUser->getUserName()), 'users'), "ok");
$editUser->fetch();
if ($editUser->getUserName() == $g_user->getUserName() && !$editUser->hasPermission('ManageUsers')) {
	camp_html_goto_page("/$ADMIN/");
}
camp_html_goto_page("/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId());

?>
