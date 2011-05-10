<?php
/**
 * @package Campsite
 */

use Newscoop\Entity\User\Subscriber;

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/UserType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');


/**
 * @package Campsite
 */
class User extends DatabaseObject {
    var $m_dbTableName = 'liveuser_users';
    var $m_keyColumnNames = array('Id');
    var $m_keyIsAutoIncrement = true;
    var $m_config = array();
    var $m_columnNames = array(
        'Id',
        'KeyId',
        'Name',
        'UName',
        'Password',
        'EMail',
        'Reader',
        'fk_user_type',
        'City',
        'StrAddress',
        'State',
        'CountryCode',
        'Phone',
        'Fax',
        'Contact',
        'Phone2',
        'Title',
        'Gender',
        'Age',
        'PostalCode',
        'Employer',
        'EmployerType',
        'Position',
        'Interests',
        'How',
        'Languages',
        'Improvements',
        'Pref1',
        'Pref2',
        'Pref3',
        'Pref4',
        'Field1',
        'Field2',
        'Field3',
        'Field4',
        'Field5',
        'Text1',
        'Text2',
        'Text3',
        'time_updated',
        'time_created',
        'lastLogin',
        'isActive',
        'password_reset_token');

    private static $m_defaultConfig = array();

    /**
     * A user of the system is a frontend reader or a 'admin' user, meaning
     * they have login rights to the backend.
     *
     * @param int $p_userId
     */
    public function User($p_userId = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_userId) && ($p_userId > 0)) {
            $this->m_data['Id'] = $p_userId;
            if ($this->keyValuesExist()) {
                $this->fetch();
            }
        }
    } // constructor


    /**
     * Creates a new user.
     *
     * @param array
     *    $p_values The user data
     *
     * @param bool
     *    TRUE on success, FALSE on failure
     */
    public function create($p_values = null)
    {
        global $controller;

        if (is_array($p_values)) {
            $p_values['time_created'] = strftime("%Y-%m-%d %H:%M:%S", time());
        }

        foreach ($p_values as $key => $value) {
            if ($key == 'UName') {
                $key = 'handle';
            }
            $values[$key] = $value;
        }

        $values['perm_type'] = 1;

        try {
            $subscriber = new Subscriber;
            $repository = $controller->getHelper('entity')->getRepository($subscriber);
            $repository->save($subscriber, $values);
            $controller->getHelper('entity')->flushManager();

            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('User account "$1" ($2) created', $subscriber->getName(), $subscriber->getUsername());
            Log::Message($logtext, null, 51);
            return true;
        } catch (Exception $e) {
            return false;
        }
    } // fn create


    /**
     * Deletes the user.
     * This will delete all config values and subscriptions of the user.
     *
     * @return boolean
     */
    public function delete()
    {
        global $g_ado_db, $LiveUserAdmin;

        if ($this->exists()) {
        	$res = $g_ado_db->Execute("SELECT Id FROM Subscriptions WHERE IdUser = ".$this->m_data['Id']);
        	while ($row = $res->FetchRow()) {
        		$g_ado_db->Execute("DELETE FROM SubsSections WHERE IdSubscription=".$row['Id']);
        	}
        	$g_ado_db->Execute("DELETE FROM Subscriptions WHERE IdUser=".$this->m_data['Id']);
        	$g_ado_db->Execute("DELETE FROM SubsByIP WHERE IdUser=".$this->m_data['Id']);
            $params = array('filters' => array('auth_user_id' => $this->m_liveUserData['auth_user_id']));
            $permData = $LiveUserAdmin->perm->getUsers($params);
            if (!is_array($permData) || sizeof($permData) < 1) {
                return false;
            }
            if ($LiveUserAdmin->removeUser($permData[0]['perm_user_id'])) {
                if (function_exists("camp_load_translation_strings")) {
                    camp_load_translation_strings("api");
                }
                $logtext = getGS('The user account "$1" ($2) has been deleted.', $this->m_data['Name'], $this->m_data['UName']);
                Log::Message($logtext, null, 52);
                return true;
            }
        }

        return false;
    } // fn delete


    /**
     * Get the user from the database.
     *
     * @param array $p_recordSet
     *
     * @return void
     */
    public function fetch($p_recordSet = null)
    {
        global $g_ado_db;

        $success = parent::fetch($p_recordSet);
        if ($success) {
            $this->m_exists = true;
        }
    } // fn fetch


    /**
     * Fetch the user by given username
     *
     * @param string
     *    $p_username The user name
     * @param bool
     *    $p_adminOnly Whether we want to be sure to get only an admin user
     *
     * @return mixed
     *    null No one user found
     *    object User object
     */
    public static function FetchUserByName($p_username, $p_adminOnly = false)
    {
        global $g_ado_db;

        $queryStr = "SELECT * FROM liveuser_users WHERE UName='$p_username'";
        if ($p_adminOnly) {
            $queryStr .= " AND Reader='N'";
        }
        $row = $g_ado_db->GetRow($queryStr);
        if ($row) {
            $user = new User();
            $user->fetch($row);
            return $user;
        }
        return null;
    } // fn FetchUserByName


    /**
     * Fetch the user by given email
     *
     * @param string
     *    $p_email The email
     * @param bool
     *    $p_adminOnly Whether we want to be sure to get only an admin user
     *
     * @return mixed
     *    null No one user found
     *    object User object
     */
    public static function FetchUserByEmail($p_email, $p_adminOnly = false)
    {
        global $g_ado_db;

        $queryStr = "SELECT * FROM liveuser_users WHERE EMail='$p_email'";
        if ($p_adminOnly) {
            $queryStr .= " AND Reader='N'";
        }
        $row = $g_ado_db->GetRow($queryStr);
        if ($row) {
            $user = new User();
            $user->fetch($row);
            return $user;
        }
        return null;
    } // fn FetchUserByEmail


    /**
     * Return the user type if there is one, or null if not.
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->m_data['fk_user_type'];
    } // fn getUserType


    /**
     * Set the user to the given user type.
     *
     * @param string $p_userType
     *
     * @return void
     */
    public function setUserType($p_userTypeId)
    {
        global $g_ado_db, $LiveUserAdmin;

        if (!$this->exists() || !is_numeric($p_userTypeId)) {
            return;
        }

        // if current user type is the same as p_userTypeId, do nothing
        if ($this->getUserType() == $p_userTypeId) {
            return;
        }

        // fetch the given user type
        $userType = new UserType($p_userTypeId);
        if ($userType->exists()) {
            // delete permissions at user level if any
            $queryStr = 'DELETE FROM liveuser_userrights '
                       .'WHERE perm_user_id = '.$this->getPermUserId();
            $g_ado_db->Execute($queryStr);
            // current user type is different than p_userTypeId
            if ($this->getUserType() != $p_userTypeId) {
                $filter = array('group_id' => $this->getUserType(),
                                'perm_user_id' => $this->getPermUserId());
                $LiveUserAdmin->perm->removeUserFromGroup($filter);
            }
            // add this user to the given user type
            $data = array('group_id' => $p_userTypeId,
                          'perm_user_id' => $this->getPermUserId());
            $LiveUserAdmin->perm->addUserToGroup($data);

            // update the user type in the user table
            $this->setProperty('fk_user_type', $p_userTypeId);
            $this->fetch();

            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('User permissions for "$1" ($2) changed', $this->m_data['Name'], $this->m_data['UName']);
            Log::Message($logtext, null, 55);
        }
    } // fn setUserType


    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->m_data['Id'];
    } // fn getUserId


    /**
     * Get the user identifier.
     *
     * @return int
     *
     * TODO: This could be an alias of getUserId() since both of them
     *       refer to the same data.
     */
    public function getAuthUserId()
    {
        return $this->m_liveUserData['auth_user_id'];
    } // fn getAuthUserId


    /**
     * Get the user identifier for the permissions configuration.
     *
     * @return int
     */
    public function getPermUserId()
    {
        return $this->m_liveUserData['perm_user_id'];
    } // fn getPermUserId


    /**
     * Get unique login key for this user - login key is only good for the
     * time the user is logged in.
     *
     * @return int
     */
    public function getKeyId()
    {
        return $this->m_data['KeyId'];
    } // fn getKeyId


    /**
     * Get the real name of the user.
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->m_data['Name'];
    } // fn getRealName


    /**
     * Get the login name of the user.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->m_data['UName'];
    } // fn getUserName


    /**
     * Get the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->m_data['Password'];
    } // fn getPassword


    /**
     * Get the email address of the user.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->m_data['EMail'];
    } // fn getEmail


    public function getPasswordResetToken()
    {
        return $this->m_data['password_reset_token'];
    }


    /**
     * Return the value of the given variable name.
     * If the variable name does not exist, return null.
     *
     * @param string $p_varName
     *
     * @return mixed
     */
    public function getConfigValue($p_varName)
    {
        if (isset($this->m_config[$p_varName])) {
            return $this->m_config[$p_varName];
        } else {
            return null;
        }
    } // fn getConfigValue


    /**
     * Set the user variable to the given value.
     * If the variable does not exist, it will be created.
     *
     * @param string $p_varName
     * @param mixed $p_value
     *
     * @return void
     *
     * TODO: Check it out. It is unused so far.
     */
    public function setConfigValue($p_varName, $p_value)
    {
		global $LiveUser, $LiveUserAdmin;

        if (!$this->exists() || empty($p_varName) || !is_string($p_varName)) {
            return;
        }

        // get the id for the given right name
        $filter = array('filters' => array('right_define_name' => $p_varName));
        $right = $LiveUserAdmin->perm->getRights($filter);
        if (!is_array($right) || sizeof($right) < 1) {
            return;
        }

        if (strtolower($p_varName) == "reader") {
            // special case for the "Reader" property
            $this->setProperty("Reader", $p_value);
        } else {
            // check for user type
            $oldType = UserType::GetUserTypeFromConfig($this->m_config);
            if (!empty($oldType)) { // must set all not to delete permissions #2364
                $permissions = $oldType->m_config;
                $permissions[$p_varName] = $p_value;
                $this->updatePermissions($permissions);
                return;
            }

            $rightId = $right[0]['right_id'];
            $params = array('right_id' => $rightId,
                            'perm_user_id' => $this->getPermUserId());
            if (isset($this->m_config[$p_varName])) {
                if (!$p_value) {
                    $LiveUserAdmin->perm->revokeUserRight($params);
                }
            } elseif ($p_value) {
                $LiveUserAdmin->perm->grantUserRight($params);
            }
            // update the auth and perm user data to reload changes
            $LiveUser->updateProperty(true, true);

            // Figure out the new User Type for the user.
            $userType = UserType::GetUserTypeFromConfig($this->m_config);
            if ($userType) {
                $this->setProperty('fk_user_type', $userType->getName());
            } else {
                $this->setProperty('fk_user_type', 'NULL', true, true);
            }
        }
    } // fn setConfigValue


    /**
     * Get the user config variables in the form array("varname" => "value").
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->m_config;
    } // fn getConfig


    /**
     * Get the default config for all users.
     *
     * @return array
     */
    public static function GetDefaultConfig()
    {
        global $g_ado_db;
        static $pluginConfig;

        // loads rights default config from database
        $queryStr = 'SELECT right_define_name FROM liveuser_rights';
        $rights = $g_ado_db->GetAll($queryStr);
        if (!is_array($rights) || sizeof($rights) <= 0) {
            $rights = array();
        }
        foreach ($rights as $right) {
            self::$m_defaultConfig[$right['right_define_name']] = 'N';
        }

        if (empty($pluginConfig)) {
            $pluginConfig = true;
            foreach (CampPlugin::GetPluginsInfo(true) as $info) {
            	self::$m_defaultConfig += $info['userDefaultConfig'];
            }     
        }
        return self::$m_defaultConfig;
    } // fn GetDefaultConfig


    /**
     * Return true if the user has the permission specified.
     *
     * @param string $p_permissionString
     *
     * @return boolean
     */
    public function hasPermission($p_permissionString)
    {
        return array_key_exists($p_permissionString, $this->m_config);
    } // fn hasPermission


    /**
     * Set the specified permission enabled or disabled.
     *
     * @param string $p_permissionString
     * @param boolean $p_value
     *
     * @return void
     */
    public function setPermission($p_permissionString, $p_value)
    {
        $this->setConfigValue($p_permissionString, $p_value);
    } // fn setPermission


    /**
     * Updates the permissions for the user.
     *
     * @param array
     *    $p_permissions The list of permissions
     *
     * @return bool
     */
    public function updatePermissions($p_permissions)
    {
        global $LiveUserAdmin;

	if (!is_array($p_permissions) || sizeof($p_permissions) == 0) {
	    return false;
	}

	$permissions = array();
        // generate an array of granted permissions
        foreach ($p_permissions as $permission => $value) {
            if ($value) {
                $permissions[$permission] = $value;
            }
        }
        // find out whether the given config matches an specific user type
        $userType = UserType::GetUserTypeFromConfig($permissions);
        if ($userType) {
            $this->setUserType($userType->getId());
            $this->setProperty('fk_user_type', $userType->getId());
        } else {
            foreach ($p_permissions as $permission => $value) {
                $filter = array('filters' => array('right_define_name' => $permission));
                $right = $LiveUserAdmin->perm->getRights($filter);
		if (!is_array($right) || sizeof($right) == 0) {
		    continue;
		}
                $params = array('right_id' => $right[0]['right_id'],
                                'perm_user_id' => $this->getPermUserId());
                // revoke or grant the given right
                if (isset($this->m_config[$permission])) {
                    if (!$value) {
                        $LiveUserAdmin->perm->revokeUserRight($params);
                        unset($this->m_config[$permission]);
                    }
                }
                if ($value) {
                    $LiveUserAdmin->perm->grantUserRight($params);
                }
            }
            // remove this user from current user type if any
            $filter = array('group_id' => $this->getUserType(),
                            'perm_user_id' => $this->getPermUserId());
            $LiveUserAdmin->perm->removeUserFromGroup($filter);
            $this->setProperty('fk_user_type', 'NULL', true, true);
        }
    } // fn updatePermissions


    /**
     * Return TRUE if this user is an administrator.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return ($this->m_data['Reader'] == 'N');
    } // fn isAdmin


    /**
     * Check if the password is a valid password in the old format.
     *
     * @return boolean
     */
    public function isValidOldPassword($p_password)
    {
        global $g_ado_db;

        $queryStr = "SELECT PASSWORD('".$g_ado_db->escape($p_password)."') AS old_password_1, "
                    ." OLD_PASSWORD('".$g_ado_db->escape($p_password)."') AS old_password_2"
                    ." FROM liveuser_users "
                    ." WHERE Id = '".$this->m_data['Id']."' ";
        if (!($row = $g_ado_db->GetRow($queryStr))) {
            return false;
        }
        // Check if the given password matches the one in the database
        if (($this->m_data['Password'] == $row['old_password_1'])
                || ($this->m_data['Password'] == $row['old_password_2'] ) ) {
            return true;
        }
        return false;
    } // fn isValidOldPassword


    /**
     * Return TRUE if the given password matches the one in the database.
     *
     * @param string
     *    $p_password
     * @param boolean
     *    $p_isEncrypted Set to true if the password is already encrypted
     *                   in SHA1 format.
     *
     * @return boolean
     */
    public function isValidPassword($p_password, $p_isEncrypted = false)
    {
        global $g_ado_db;

        if (!$p_isEncrypted) {
            $queryStr = "SELECT SHA1('".$g_ado_db->escape($p_password)."') as encrypted_password "
                       ."FROM liveuser_users "
                       ."WHERE Id = '".$this->m_data['Id']."' ";
            $encryptedPassword = $g_ado_db->GetOne($queryStr);
            return ($encryptedPassword == $this->getPassword());
        }
        return ($p_password == $this->m_data['Password']);
    } // fn isValidPassword


    /**
     * @param string $p_password
     *
     * @return void
     */
    public function setPassword($p_password, $p_commit = true)
    {
        global $g_ado_db;

        $queryStr = "SELECT SHA1('".$g_ado_db->escape($p_password)."') AS PWD";
        $row = $g_ado_db->GetRow($queryStr);
        $this->setProperty('Password', $row['PWD'], $p_commit);
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Password changed for user "$1" ($2)', $this->m_data['Name'], $this->m_data['UName']);
        Log::Message($logtext, null, 54);
        $this->setProperty('password_reset_token','');
    }  // fn setPassword

    /**
     * Generates token
     *
     * @return void
     */
    public function setPasswordResetToken()
    {
        $this->setProperty('password_reset_token', sha1(uniqid('', TRUE)) . (string) time());
    }


    /**
     * Initialize the per-session login key. This makes sure the user can only
     * login from one location at a time.
     *
     * @return void
     */
    public function initLoginKey()
    {
        // Generate the Key ID
        $this->setProperty('KeyId', 'RAND()*1000000000+RAND()*1000000+RAND()*1000', true, true);
    } // fn initLoginKey


    /**
     * Return true if the user name exists.
     *
     * @param string $p_userName
     *
     * @return boolean
     */
    public static function UserNameExists($p_userName)
    {
        global $g_ado_db;

        $sql = "SELECT UName FROM liveuser_users "
              ."WHERE UName='".$g_ado_db->escape($p_userName)."'";
        if ($g_ado_db->GetOne($sql)) {
            return true;
        } else {
            return false;
        }
    } // fn UserNameExists


    /**
     * Return wheather a user exists with the given e-mail address.
     *
     * @param string
     *    $p_email The e-mail address to look for
     * @param string
     *    $p_userName (optional) The user name
     *
     * @return boolean
     *    TRUE if the e-mail address already exists, otherwise FALSE
     */
    public static function EmailExists($p_email, $p_userName = null)
    {
        global $g_ado_db;

        $sql = "SELECT UName, EMail FROM liveuser_users "
              ."WHERE EMail = '".$g_ado_db->escape($p_email)."'";
        $row = $g_ado_db->GetOne($sql);
        if (!$row) {
            return false;
        }
        if (!is_null($p_userName)) {
            if ($row['UName'] == $p_userName) {
                return false;
            }
        }

        return true;
    } // fn EmailExists


    /**
     * Get all users matching the given parameters.
     *
     * @param boolean $p_onlyAdmin
     * @param string $p_userType
     *
     * @return array
     */
    public static function GetUsers($p_onlyAdmin = true, $p_userType = null)
    {
        global $g_ado_db;

        $constraints = array();
        if ($p_onlyAdmin) {
            $constraints[] = "Reader='N'";
        }
        if (!is_null($p_userType)) {
            $constraints[] = "fk_user_type='".$p_userType."'";
        }
        if (count($constraints) > 0) {
            $whereStr = " WHERE ".implode(" AND ", $constraints);
        }
        $sql = "SELECT * FROM liveuser_users " . $whereStr;
        return DbObjectArray::Create("User", $sql);
    } // fn GetUsers


    /**
     * Sync campsite and phorum users.
     */
    public function syncPhorumUser()
    {
        $phorumUser = Phorum_user::GetByUserName($this->m_data['UName']);
        if ($phorumUser->setPassword($this->m_data['Password'])
                && $phorumUser->setEmail($this->m_data['EMail'])) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Base data synchronized to phorum user for "$1" ($2)', $this->m_data['Name'], $this->m_data['UName']);
            Log::Message($logtext, null, 161);
        }
    } // fn syncPhorumUser

    /**
     * Sync all campsite and phorum users.
     *
     * @return void
     */
    public static function SyncPhorumUsers()
    {
        require_once($GLOBALS['g_campsiteDir'].'/include/phorum_load.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_user.php');

        $g_ado_db = $GLOBALS['g_ado_db'];
        $queryStr = 'SELECT u.Id, pu.user_id, u.UName, u.Password, u.EMail
                        FROM liveuser_users AS u LEFT JOIN phorum_users AS pu
                            ON u.UName = pu.username
                        WHERE fk_campsite_user_id IS NULL
                            OR fk_campsite_user_id != u.Id';
        $nullUsers = $g_ado_db->GetAll($queryStr);
        if (!is_array($nullUsers) || empty($nullUsers)) { // all synced
            return;
        }

        foreach ($nullUsers as $nullUser) {
            if (empty($nullUser['user_id'])) {
                $phorumUser = new Phorum_user();
                $phorumUser->create($nullUser['UName'], $nullUser['Password'],
                                    $nullUser['EMail'], $nullUser['Id'], true);
            } else {
                $sql = 'UPDATE phorum_users SET fk_campsite_user_id = NULL
                        WHERE fk_campsite_user_id = ' . $nullUser['Id'];
                $g_ado_db->Execute($sql);
                $sql = 'UPDATE phorum_users SET fk_campsite_user_id = ' . $nullUser['Id'] . '
                        WHERE user_id = ' . $nullUser['user_id'];
                $g_ado_db->Execute($sql);
            }
        }
    } // fn SyncPhorumUsers

} // class User

?>
