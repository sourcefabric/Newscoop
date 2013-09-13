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
if (!$canManage) {
	camp_html_display_error($translator->trans('You do not have the right to create user accounts.', array(), 'users'));
	exit;
}

// define fields arrays
$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
$notNullFields = array('UName'=>'Account name', 'Name'=>'Full Name', 'EMail'=>'E-Mail', 'Type'=>'Type');

// read fields values
$errorField = "";
$typeParam = 'uType=' . urlencode($uType);
$backLink = "/$ADMIN/users/edit.php?$typeParam";
foreach ($fields as $index=>$field) {
	$fieldValues[$field] = Input::Get($field, 'string', null);
	if ($fieldValues[$field] != '')
		$backLink .= "&" . urlencode($field) . "=" . urlencode($fieldValues[$field]);
	elseif (array_key_exists($field, $notNullFields) && $errorField == "")
		$errorField = $field;
}

// set the Reader field
$Type = Input::Get('Type', 'int', 0);
$fieldValues['Reader'] = ($uType == 'Subscribers') ? 'Y' : 'N';
if ( ($uType == 'Staff') && !$Type && ($errorField == '') ) {
	$errorField = 'Type';
}

// display errors if found
if ($errorField != "") {
	$desc = $notNullFields[$errorField];
	if ($errorField == 'Type') {
		$errorMsg = $translator->trans('You must select a $1', array('$1' => $desc), 'users');
	} else {
		$errorMsg = $translator->trans('You must fill in the $1 field.', array('$1' => $desc), 'users');
	}
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
}

if (User::UserNameExists($fieldValues['UName'])) {
	$errorMsg = $translator->trans('That user name already exists, please choose a different login name.', array(), 'users');
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
}

if (User::EmailExists($fieldValues['EMail'])) {
    $errorMsg = $translator->trans('Another user is registered with that e-mail address, please choose a different one.', array(), 'users');
    camp_html_add_msg($errorMsg);
    camp_html_goto_page($backLink);
}

// read password
$password = Input::Get('password', 'string', '');
$passwordConf = Input::Get('passwordConf', 'string', '');
if (strlen($password) < 6 || $password != $passwordConf) {
	$errorMsg = $translator->trans('The password must be at least 6 characters long and both passwords should match.', array(), 'users');
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
}
$fieldValues['passwd'] = $password;

// create user
$editUser = new User();

if ($editUser->create($fieldValues)) {
    if ($uType == 'Staff') {
        $editUser->setUserType($Type);
    }
    camp_html_add_msg($translator->trans('User account $1 was created successfully.', array('$1' => $editUser->getUserName()), 'users'), "ok");
    camp_html_goto_page("/$ADMIN/users/edit.php?User=".$editUser->getUserId()."&$typeParam");
} else {
    camp_html_add_msg($translator->trans('The user account could not be created.', array(), 'users'));
    camp_html_goto_page($backLink);
}

?>
