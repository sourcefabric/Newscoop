<?php

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/Log.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/UserType.php');

if (!SecurityToken::isValid()) {
	camp_html_display_error(getGS('Invalid security token!'));
	exit;
}

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($g_user, $canManage, $canDelete);

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'), "/$ADMIN/users/?".get_user_urlparams());
	exit;
}

if (!$canManage && $editUser->getUserId() != $g_user->getUserId()) {
	$errMsg = getGS('You do not have the right to change user account information.');
	camp_html_display_error($errMsg);
	exit;
}

$typeParam = 'uType=' . urlencode($uType);
$userEmail = Input::Get('EMail', 'string', 0);
if ($userEmail != $editUser->getEmail()) {
    if (User::EmailExists($userEmail, $editUser->getUserName())) {
        $backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId();
        $errMsg = getGS('Another user is registered with that e-mail address, please choose a different one.');
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
			camp_html_add_msg(getGS('The password you typed is incorrect.'));
			camp_html_goto_page($backLink);
		}
	}
	if (strlen($password) < 6 || $password != $passwordConf) {
		camp_html_add_msg(getGS('The password must be at least 6 characters long and both passwords should match.'));
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
    camp_html_add_msg(getGS('There was an error when trying to update the user info.'));
    camp_html_goto_page($backLink);
} else {
    $editUser->commit();
}

$logtext = getGS('User account information changed for "$1"', $editUser->getUserName());
Log::Message($logtext, $g_user->getUserId(), 56);

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

	$logtext = getGS('Permissions changed for user "$1"',$editUser->getUserName());
	Log::Message($logtext, $g_user->getUserId(), 55);
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

camp_html_add_msg(getGS("User '$1' information was changed successfully.",
	$editUser->getUserName()), "ok");
$editUser->fetch();
if ($editUser->getUserName() == $g_user->getUserName() && !$editUser->hasPermission('ManageUsers')) {
	camp_html_goto_page("/$ADMIN/");
}
camp_html_goto_page("/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId());

?>
