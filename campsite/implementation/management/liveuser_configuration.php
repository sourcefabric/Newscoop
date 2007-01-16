<?php

// Global permissions array
global $g_permissions;

// Data Source Name (DSN)
$dsn = 'mysql://'.$Campsite['DATABASE_USER']
		.':'.$Campsite['DATABASE_PASSWORD']
		.'@'.$Campsite['DATABASE_SERVER_ADDRESS']
		.'/'.$Campsite['DATABASE_NAME'];

// Define the LiveUser configuration
$liveuserConfig = array (
    'session' => array(
        'name' => 'PHPSESSID',
        'varname' => 'loginInfo'
        ),
    'logout' => array ('destroy' => true),
    'cookie' => array(
        'name' => 'loginInfo',
        'path' => null,
        'domain' => null,
        'secure' => false,
        'lifetime' => 30,
        'secret' => 'asecretkey',
        'savedir' => '.'
        ),
    'authContainers' => array (
        'DB' => array (
            'type' => 'DB',
            'expireTime' => 0,
            'idleTime' => 0,
            'allowDuplicateHandles' => 0,
            'allowEmptyPasswords' => 0,
            'passwordEncryptionMode' => 'SHA1',
            'storage' => array (
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
                'dsn' => $dsn,
                'prefix' => 'liveuser_',
                'alias' => array(),
                'fields' => array(),
                'tables' => array()
                )
            )
        )
    );


require_once($_SERVER['DOCUMENT_ROOT'].'/include/pear/LiveUser/Admin.php');

$LiveUser =& LiveUser::factory($liveuserConfig);
if (!$LiveUser->init()) {
    die();
}
$LiveUserAdmin =& LiveUser_Admin::factory($liveuserConfig);
$LiveUserAdmin->init();
$g_permissions = $LiveUserAdmin->perm->outputRightsConstants('array');

?>
