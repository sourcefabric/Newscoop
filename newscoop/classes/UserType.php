<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class UserType extends DatabaseObject
{
    const TABLE = 'liveuser_groups';

    var $m_dbTableName = self::TABLE;
    var $m_keyColumnNames = array('group_id');
    var $m_keyIsAutoIncrement = false;
    var $m_config = array();
    var $m_columnNames = array(
        'group_id',
        'group_type',
        'group_define_name',
        'role_id',
    );
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string
     *    $p_userTypeId (optional) The user type identifier
     *
     * @return void
     */
    public function UserType($p_userTypeId = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_userTypeId) && $p_userTypeId > 0) {
            $this->m_data['group_id'] = $p_userTypeId;
            if ($this->keyValuesExist()) {
                $this->fetch();
            }
        }
    } // constructor

    
    /**
     * Whether the user type exists or not
     *
     * @return bool
     *    $this->m_exists TRUE on success, FALSE on failure
     */
    public function exists()
    {
        return $this->m_exists;
    } // fn exists


	/**
     * Create the new UserType with the config variables given.
     * If a config variable is not set, the default value will be used.
     *
     * @param array
     *    $p_configVars The privileges list to be granted
     *
     * @return bool
     *    TRUE on success, FALSE on failure
     */
    public function create($p_name, $p_configVars = array())
    {
        global $LiveUserAdmin;

        $success = false;
        if (empty($p_name) || !is_string($p_name)) {
            return $success;
        }

        // create the user type via LiveUser_Admin API
        $data = array('group_define_name' => $p_name);
        $uTypeId = $LiveUserAdmin->perm->addGroup($data);
        if ($uTypeId) {
            $success = true;
            $this->m_data['group_id'] = $uTypeId;

            // user successfully created, then grant the checked rights
            $defaultConfig = User::GetDefaultConfig();
            if (sizeof($p_configVars)) {
                foreach ($p_configVars as $varname => $value) {
                    if (array_key_exists($varname, $defaultConfig)) {
                        $filter = array('filters' => array('right_define_name' => $varname));
                        $right = $LiveUserAdmin->perm->getRights($filter);
                        $data = array('right_id' => $right[0]['right_id'],
                                      'group_id' => $this->m_data['group_id']);
                        $LiveUserAdmin->perm->grantGroupRight($data);
                    }
                }
            }
        }
        // fetch user type data if it was successfully created
        $this->fetch();
        if ($this->exists()) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('User type "$1" added', $p_name);
            Log::Message($logtext, null, 121);
        }
        return $success;
    } // fn create


	/**
     * Delete the user type.
     *
     * @return bool
     *    TRUE on success, FALSE on failure
     */
    public function delete()
    {
        global $LiveUserAdmin;

        $filter = array('group_id' => $this->m_data['group_id']);
        if ($LiveUserAdmin->perm->removeGroup($filter)) {
            $this->m_exists = false;
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('User type "$1" deleted', $this->m_data['group_define_name']);
            Log::Message($logtext, null, 122);
            return true;
        }
        return false;
    } // fn delete


    /**
     * Get the id of this user type.
     *
     * @return int
     */
    public function getId()
    {
        return $this->m_data['group_id'];
    } // gn getId


	/**
     * Get the name of this user type.
     * @return string
     */
    public function getName()
    {
        return $this->m_data['group_define_name'];
    } // fn getName


	/**
     * Return the value of the given variable name.
     * If the variable name does not exist, return null.
     *
     * @param string $p_varName
     * @return mixed
     */
    public function getValue($p_varName)
    {
        if (isset($this->m_config[$p_varName])) {
            return $this->m_config[$p_varName];
        } else {
            return null;
        }
    } // fn getValue


	/**
     * Set the default config value for the given variable.
     * This creates the new config variable if it didn't exist.
     *
     * @param string $p_varName
     * @param mixed $p_value
     *
     * @return void
     */
    public function setValue($p_varName, $p_value)
    {
        global $g_ado_db;

        // translate to resource/action
        require_once dirname(__FILE__) . '/../library/Newscoop/Utils/PermissionToAcl.php';
        list($resource, $action) = Newscoop\Utils\PermissionToAcl::translate($p_varName);

        // get type
        $type = $p_value ? 'allow' : 'deny';

        // get role id
        $role = (int) $this->m_data['role_id'];

        // store
        $sql = "INSERT IGNORE
                INTO acl_rule (`type`, `role_id`, `resource`, `action`)
                VALUES ('$type', $role, '$resource', '$action')";
        $g_ado_db->Execute($sql);
    } // fn setValue


	/**
     * Return an array of config values in the form array("varname" => "value");
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->m_config;
    } // fn getConfig


    /**
     * Return true if the user type has the permission specified.
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
     * Set the specified permission.
     *
     * @param string
     *    $p_permissionString
     * @param boolean
     *    $p_permissionValue
     *
     */
	public function setPermission($p_permissionString, $p_permissionValue)
    {
        $this->setValue($p_permissionString, $p_permissionValue);
    } // fn setPermission


    /**
     * Get the user type that matches the given name.
     *
     * @param string
     *    $p_name The name of the user type
     *
     * @return mixed
     *    null If the user type does not exists or any error
     *    UserType object
     */
    public static function GetByName($p_name)
    {
        global $LiveUserAdmin;

        if (empty($p_name)) {
            return null;
        }

        $p_name = addslashes($p_name);
        $filter = array('filters' => array('group_define_name' => $p_name));
        $data = $LiveUserAdmin->perm->getGroups($filter);
        if (!is_array($data) || sizeof($data) < 1) {
            $uTypeId = null;
        } else {
            $uTypeId = $data[0]['group_id'];
        }
        $userType = new UserType($uTypeId);
        return $userType;
    } // fn GetByName


    /**
     * Get the user type that matches the given config variables.
     *
     * @param array
     *    $p_configVars An array of permissions variables
     *
     * @return mixed
     *    bool False The config passed does not match any user type
     *    object $userType The user type object matching
     */
    public static function GetUserTypeFromConfig($p_configVars)
    {
        global $LiveUserAdmin;

        if (!is_array($p_configVars) || (count($p_configVars) == 0) ) {
            return false;
        }

        $configVarsSize = sizeof($p_configVars);
        $userTypes = UserType::GetUserTypes();
        foreach ($userTypes as $userType) {
            $uTypeConfigSize = sizeof($userType->m_config);
            if ($configVarsSize > $uTypeConfigSize) {
                $diff = array_diff_key($p_configVars, $userType->m_config);
            } else {
                $diff = array_diff_key($userType->m_config, $p_configVars);
            }
            if (sizeof($diff) == 0) {
                return $userType;
            }
        }
        return false;
    } // fn GetUserTypeFromConfig


    /**
     * Get all the user types with the exception of those with
     * the Reader permission.
     *
     * @return array
     *    An array of UserType objects.
     */
    public static function GetUserTypes()
    {
        global $LiveUserAdmin;

        $userTypes = array();
        $res = $LiveUserAdmin->perm->getGroups();
        foreach ($res as $userType) {
            $tmpUserType = new UserType($userType['group_id']);
            $userTypes[] = $tmpUserType;
        }
        return $userTypes;
    } // fn GetUserTypes

} // class UserType

?>
