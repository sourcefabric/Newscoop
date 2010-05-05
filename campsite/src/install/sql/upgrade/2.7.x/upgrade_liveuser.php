<?php

$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once("$cs_dir/conf/database_conf.php");
require_once("$cs_dir/conf/install_conf.php");

if (!is_array($Campsite)) {
    echo "Invalid configuration file(s)";
    exit(1);
}

$campsite_db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

// LiveUser settings
//
// Data Source Name (DSN)
// Edit the dsn value to fit your requirements.
// Default value means LiveUser tables are in the local Campsite server.
$dsn = 'mysql://'.$db_user.':'.$db_passwd.'@'.$db_host.'/'.$campsite_db_name;

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

$localPearPath = $Campsite['CAMPSITE_DIR'].'/include/pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $localPearPath);
require_once('LiveUser/Admin.php');

$LiveUser = LiveUser::factory($liveuserConfig);
if (!$LiveUser->init()) {
    echo "Error when initializing the authentication system.";
    exit(1);
}
$LiveUserAdmin = LiveUser_Admin::factory($liveuserConfig);
$LiveUserAdmin->init();
$permissions = $LiveUserAdmin->perm->outputRightsConstants('array');

$campsiteConn = mysql_connect($db_host, $db_user, $db_passwd, true);
if (!$campsiteConn) {
    echo "Unable to connect to the database server.\n";
    exit(1);
}

if (!mysql_select_db($campsite_db_name, $campsiteConn)) {
    echo "Unable to use the database " . $db_name . ".\n";
    exit(1);
}
mysql_query("SET NAMES 'utf8'");

// Clean the liveuser_userrights table
if (!($res = mysql_query('DELETE FROM liveuser_userrights', $campsiteConn))) {
    echo "Unable to read from the database $campsite_db_name.\n";
}

// Get User types
if (!($res = mysql_query('SELECT DISTINCT(user_type_name) FROM UserTypes', $campsiteConn))) {
    echo "Unable to read from the database $campsite_db_name.\n";
}

// Create LiveUser groups (user types) and group rights from UserTypes
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    $data = array('group_define_name' => $row['user_type_name']);
    if ($group_id = $LiveUserAdmin->perm->addGroup($data)) {
        $queryStr = "SELECT varname FROM UserTypes "
                   ."WHERE user_type_name = '".$row['user_type_name']."' AND value = 'Y' "
                   ."ORDER BY varname";
        $result = mysql_query($queryStr, $campsiteConn);
        while ($utype = mysql_fetch_array($result, MYSQL_ASSOC)) {
            if (array_key_exists($utype['varname'], $permissions)) {
                $grant_data = array('right_id' => $permissions[$utype['varname']],
                                    'group_id' => $group_id);
                $LiveUserAdmin->perm->grantGroupRight($grant_data);
            }
        }
    }
}

// Get user types/groups from liveuser_groups
$user_types = array();
$groups = $LiveUserAdmin->perm->getGroups();
foreach ($groups as $group => $data) {
    $user_types[$data['group_define_name']] = $data['group_id'];
}

// Get all the campsite users
if (!($res = mysql_query("SELECT * FROM Users ORDER BY Id", $campsiteConn))) {
    echo "Unable to read from the database $campsite_db_name.\n";
    exit(1);
}

// Create LiveUser users
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    // Look for the user in the liveuser_users table
    $filter = array('container' => 'auth',
                    'filters' => array('handle' => $row['UName']));
    $user_auth_data = $LiveUserAdmin->getUsers($filter);
    if (!is_array($user_auth_data) || sizeof($user_auth_data) < 1) {
        $liveUserData = array();
        foreach ($row as $key => $value) {
            if ($key == 'UName') {
                $key = 'handle';
            } elseif ($key == 'Password') {
                $key = 'passwd';
            } elseif ($key == 'Id') {
                continue;
            }
            $liveUserData[$key] = $value;
        }
        $liveUserData['perm_type'] = 1;
        $permUserId = $LiveUserAdmin->addUser($liveUserData);
        $queryStr = "SELECT auth_user_id FROM liveuser_perm_users "
                   ."WHERE perm_user_id = '".$permUserId."'";
        $result = mysql_query($queryStr, $campsiteConn);
        if ($result) {
            $permUserData = mysql_fetch_array($result, MYSQL_ASSOC);
            $authUserId = $permUserData['auth_user_id'];
            $queryStr = "UPDATE liveuser_users SET Password = '".$row['Password']."' "
                       ."WHERE Id = '".$authUserId."'";
            mysql_query($queryStr, $campsiteConn);
        }
    } else {
        // User is already in the liveuser_users table, so we get the perm user id
        $filter = array('container' => 'perm',
                        'filters' => array('auth_user_id' => $user_auth_data[0]['auth_user_id']));
        $user_perm_data = $LiveUserAdmin->getUsers($filter);
        $permUserId = $user_perm_data[0]['perm_user_id'];
        $authUserId = $user_auth_data[0]['auth_user_id'];
    }

    // Assign user to group, if any
    if ($permUserId && !empty($row['fk_user_type']) && array_key_exists($row['fk_user_type'], $user_types)) {
        $data = array('perm_user_id' => $permUserId,
                      'group_id' => $user_types[$row['fk_user_type']]);
        $LiveUserAdmin->perm->addUserToGroup($data);
        // Update the fk_user_type field of the liveuser_users table to the corresponding value
        $qS = "UPDATE liveuser_users SET fk_user_type = ".$user_types[$row['fk_user_type']]
             ." WHERE Id = ".$authUserId;
        mysql_query($qS, $campsiteConn);
    } elseif ($permUserId && empty($row['fk_user_type'])) {
        // User does not belong to any group, we read granted rights from UserConfig
        // and assign them to liveuser_userrights
        $qS = "SELECT varname FROM UserConfig "
             ."WHERE fk_user_id = ".$row['Id']." AND value = 'Y'";
        $result = mysql_query($qS, $campsiteConn);
        while ($uc_data = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $permData = array('perm_user_id' => $permUserId,
                              'right_id' => $permissions[$uc_data['varname']],
                              'right_level' => 3);
            $LiveUserAdmin->perm->grantUserRight($permData);
        }
    }
}

// Delete tables which are no longer used
$queryStr = 'DROP TABLE Users';
mysql_query($queryStr, $campsiteConn);
$queryStr = 'DROP TABLE UserTypes';
mysql_query($queryStr, $campsiteConn);
$queryStr = 'DROP TABLE UserConfig';
mysql_query($queryStr, $campsiteConn);

?>
