<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');

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
    $liveUserValues[$value] = Input::Get($value, 'string', '');
    $editUser->setProperty($value, $liveUserValues[$value], false);
}

$backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId();
if ($LiveUserAdmin->updateUser($liveUserValues, $editUser->getPermUserId()) === false) {
    camp_html_add_msg(getGS('There was an error when trying to update the user info.'));
    camp_html_goto_page($backLink);
} else {
    $editUser->commit();
}

$logtext = getGS('User account information changed for $1', $editUser->getUserName());
Log::Message($logtext, $g_user->getUserName(), 56);

// sync base data to the corresponding phorum user
$isPhorumUser = Phorum_user::GetByUserName($editUser->getUserName());
if($isPhorumUser) {
    $editUser->syncPhorumUser();
}

if ($editUser->isAdmin() && $customizeRights && $canManage) {
	// save user customized rights
	$rightsFields = $editUser->GetDefaultConfig();
	$permissions = array();
	foreach ($rightsFields as $field=>$value) {
		$val = Input::Get($field, 'string', 'off');
		$permissionEnabled = ($val == 'on') ? true : false;
		$permissions[$field] = $permissionEnabled;
	}
    // set permissions into LiveUser
    foreach ($permissions as $perm => $value) {
        $updateData = array('perm_user_id' => $editUser->getPermUserId(),
                            'right_id' => $g_permissions[$perm]
                            );
        if ($value == true) {
            $updateData['right_level'] = 1;
            $LiveUserAdmin->perm->grantUserRight($updateData);
        } else {
            $LiveUserAdmin->perm->revokeUserRight($updateData);
        }
    }

	$editUser->updatePermissions($permissions);

	$logtext = getGS('Permissions for $1 changed',$editUser->getUserName());
	Log::Message($logtext, $g_user->getUserName(), 55);
}
if ($editUser->isAdmin() && !$customizeRights && $canManage) {
	// save user rights based on existing user type
	$userTypeName = Input::Get('UserType', 'string', '');
	if ($userTypeName != "") {
		$editUser->setUserType($userTypeName);
	}
}

camp_html_add_msg(getGS("User '$1' information was changed successfully.",
	$editUser->getUserName()), "ok");
$editUser->fetch();
if ($editUser->getUserName() == $g_user->getUserName() && !$editUser->hasPermission('ManageUsers')) {
	camp_html_goto_page("/$ADMIN/");
}
camp_html_goto_page("/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getUserId());

?>
