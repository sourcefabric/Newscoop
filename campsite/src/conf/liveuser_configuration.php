<?php

require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');
require_once('DB.php');

// Global permissions array
global $g_permissions;
global $Campsite;
global $LiveUser;
global $LiveUserAdmin;


// Data Source Name (DSN)
$dsn = 'mysql://'.$Campsite['db']['user']
            .':'.$Campsite['db']['pass']
            .'@'.$Campsite['db']['host']
            .'/'.$Campsite['db']['name'];

$db = DB::connect($dsn);
if (PEAR::isError($db)) {
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
?>
        <font color="red" size="3">
        <p>ERROR connecting to the MySQL server!</p>
        <p>Please start the MySQL database server and verify if the connection configuration is valid.</p>
        </font>
<?php
    exit(0);
}

// Define the LiveUser configuration
$liveuserConfig = array (
    'session' => array(
        'name' => 'PHPSESSID',
        'varname' => 'loginInfo'
        ),
    'session_cookie_params' => array(
        'lifetime' => $Campsite['campsite']['session_lifetime'] / 86400,
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => true
        ),
    'login' => array('regenid' => true),
    'logout' => array ('destroy' => true),
//    'cookie' => array(
//        'name' => 'csRmeInfo',
//        'path' => '/',
//        'domain' => null,
//        'secure' => false,
//        'lifetime' => 30,
//        'secret' => $Campsite['campsite']['secret_key'],
//        'savedir' => '.',
//        'secure' => false,
//        'httponly' => true
//        ),
    'authContainers' => array (
        'DB' => array (
            'type' => 'DB',
            'expireTime' => 0,
            'idleTime' => 0,
            'allowDuplicateHandles' => 0,
            'allowEmptyPasswords' => 0,
            'passwordEncryptionMode' => 'SHA1',
            'storage' => array (
		'connection' => $db,
                'dsn' => $dsn,
                'alias' => array (
                    'auth_user_id' => 'Id',
                    'handle' => 'UName',
                    'passwd' => 'Password',
                    'lastlogin' => 'lastLogin',
                    'is_active' => 'isActive',
                    'Name' => 'Name',
                    'KeyId' => 'KeyId',
                    'fk_user_type' => 'fk_user_type',
                    'EMail' => 'EMail',
                    'Reader' => 'Reader',
                    'City' => 'City',
                    'StrAddress' => 'StrAddress',
                    'State' => 'State',
                    'CountryCode' => 'CountryCode',
                    'Phone' => 'Phone',
                    'Fax' => 'Fax',
                    'Contact' => 'Contact',
                    'Phone2' => 'Phone2',
                    'Title' => 'Title',
                    'Gender' => 'Gender',
                    'Age' => 'Age',
                    'PostalCode' => 'PostalCode',
                    'Employer' => 'Employer',
                    'EmployerType' => 'EmployerType',
                    'Position' => 'Position',
                    'Interests' => 'Interests',
                    'How' => 'How',
                    'Languages' => 'Languages',
                    'Improvements' => 'Improvements',
                    'Pref1' => 'Pref1',
                    'Pref2' => 'Pref2',
                    'Pref3' => 'Pref3',
                    'Pref4' => 'Pref4',
                    'Field1' => 'Field1',
                    'Field2' => 'Field2',
                    'Field3' => 'Field3',
                    'Field4' => 'Field4',
                    'Field5' => 'Field5',
                    'Text1' => 'Text1',
                    'Text2' => 'Text2',
                    'Text3' => 'Text3',
                    'time_updated' => 'time_updated',
                    'time_created' => 'time_created'
                    ),
                'fields' => array (
                    'lastlogin' => 'timestamp',
                    'is_active' => 'boolean',
                    'Name' => 'text',
                    'KeyId' => 'text',
                    'fk_user_type' => 'text',
                    'EMail' => 'text',
                    'Reader' => 'text',
                    'City' => 'text',
                    'StrAddress' => 'text',
                    'State' => 'text',
                    'CountryCode' => 'text',
                    'Phone' => 'text',
                    'Fax' => 'text',
                    'Contact' => 'text',
                    'Phone2' => 'text',
                    'Title' => 'text',
                    'Gender' => 'text',
                    'Age' => 'text',
                    'PostalCode' => 'text',
                    'Employer' => 'text',
                    'EmployerType' => 'text',
                    'Position' => 'text',
                    'Interests' => 'text',
                    'How' => 'text',
                    'Languages' => 'text',
                    'Improvements' => 'text',
                    'Pref1' => 'text',
                    'Pref2' => 'text',
                    'Pref3' => 'text',
                    'Pref4' => 'text',
                    'Field1' => 'text',
                    'Field2' => 'text',
                    'Field3' => 'text',
                    'Field4' => 'text',
                    'Field5' => 'text',
                    'Text1' => 'text',
                    'Text2' => 'text',
                    'Text3' => 'text',
                    'time_updated' => 'timestamp',
                    'time_created' => 'timestamp'
                    ),
                'tables' => array (
                    'users' => array (
                        'fields' => array (
                            'lastlogin' => false,
                            'is_active' => false,
                            'Name' => false,
                            'EMail' => false,
                            'KeyId' => false,
                            'fk_user_type' => false,
                            'Reader' => false,
                            'City' => false,
                            'StrAddress' => false,
                            'State' => false,
                            'CountryCode' => false,
                            'Phone' => false,
                            'Fax' => false,
                            'Contact' => false,
                            'Phone2' => false,
                            'Title' => false,
                            'Gender' => false,
                            'Age' => false,
                            'PostalCode' => false,
                            'Employer' => false,
                            'EmployerType' => false,
                            'Position' => false,
                            'Interests' => false,
                            'How' => false,
                            'Languages' => false,
                            'Improvements' => false,
                            'Pref1' => false,
                            'Pref2' => false,
                            'Pref3' => false,
                            'Pref4' => false,
                            'Field1' => false,
                            'Field2' => false,
                            'Field3' => false,
                            'Field4' => false,
                            'Field5' => false,
                            'Text1' => false,
                            'Text2' => false,
                            'Text3' => false,
                            'time_updated' => false,
                            'time_created' => false
                            )
                        )
                    )
                )
            )
        ),
    'permContainer' => array (
        'type' => 'Medium',
        'storage' => array(
            'DB' => array (
		'connection' => $db,
                'dsn' => $dsn,
                'prefix' => 'liveuser_',
                'alias' => array(),
                'fields' => array(),
                'tables' => array()
                )
            )
        )
    );


require_once(CS_PATH_PEAR_LOCAL.DIR_SEP.'LiveUser'.DIR_SEP.'Admin.php');

$GLOBALS['LiveUser'] = LiveUser::singleton($liveuserConfig);
if (!$GLOBALS['LiveUser']->init()) {
    exit(0);
}
$GLOBALS['LiveUserAdmin'] = LiveUser_Admin::singleton($liveuserConfig);
if (!$GLOBALS['LiveUserAdmin']->init()) {
    exit(0);
}

$g_permissions = $GLOBALS['LiveUserAdmin']->perm->outputRightsConstants('array');

?>
