<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */
class UserType extends DatabaseObject {
    var $m_dbTableName = 'liveuser_groups';
    var $m_keyColumnNames = array('group_id');
    var $m_keyIsAutoIncrement = false;
    var $m_config = array();
    var $m_columnNames = array('group_id',
                               'group_type',
                               'group_define_name');
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string
     *    $p_userTypeId (optional) The user type identifier
     *
     * @return void
     */
    function UserType($p_userTypeId = null)
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
     * Get the user type from LiveUserAdmin.
     *
     * @return void
     */
    function fetch()
    {
        global $g_ado_db, $LiveUserAdmin;

        // get the group data
        $filter = array('filters' => array('group_id' => $this->m_data['group_id']));
        $group = $LiveUserAdmin->perm->getGroups($filter);
        if (!is_array($group) || sizeof($group) < 1) {
            return false;
        }
        // populate m_data
        foreach ($group[0] as $columnName => $value) {
            if (in_array($columnName, $this->m_columnNames)) {
                $this->m_data[$columnName] = $value;
            }
        }
        $this->m_exists = true;

        // get the permissions config
        $queryStr = 'SELECT r.right_id as value, '
                          .'r.right_define_name as varname '
                   .'FROM liveuser_groups as g, '
                        .'liveuser_rights as r, '
                        .'liveuser_grouprights as l '
                   .'WHERE g.group_id=l.group_id AND '
                         .'r.right_id=l.right_id AND '
                         .'g.group_id='.$this->m_data['group_id'];
        $config = $g_ado_db->GetAll($queryStr);
        if ($config) {
            // Make m_config an associative array
            foreach ($config as $value) {
                $this->m_config[$value['varname']] = $value['value'];
            }
        }
    } // fn fetch


    /**
     * Whether the user type exists or not
     *
     * @return bool
     *    $this->m_exists TRUE on success, FALSE on failure
     */
    function exists()
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
    function create($p_name, $p_configVars = array())
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
        // fetch user type data if it was successfully created
        $this->fetch();
        if ($this->exists()) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('User type $1 added', $p_name);
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
    function delete()
    {
        global $LiveUserAdmin;

        $filter = array('group_id' => $this->m_data['group_id']);
        if ($LiveUserAdmin->perm->removeGroup($filter)) {
            $this->m_exists = false;
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('User type $1 deleted', $this->m_data['group_define_name']);
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
    function getId()
    {
        return $this->m_data['group_id'];
    } // gn getId


	/**
     * Get the name of this user type.
     * @return string
     */
    function getName()
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
    function getValue($p_varName)
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
    function setValue($p_varName, $p_value)
    {
        global $g_ado_db, $LiveUserAdmin;

        // get the id for the given right name
        $filter = array('filters' => array('right_define_name' => $p_varName));
        $right = $LiveUserAdmin->perm->getRights($filter);
        if (!is_array($right) || sizeof($right) < 1) {
            return;
        }
        $rightId = $right[0]['right_id'];
        $params = array('right_id' => $rightId,
                        'group_id' => $this->m_data['group_id']);
        // revoke or grant the given right
        if (isset($this->m_config[$p_varName])) {
            if (!$p_value) {
                $LiveUserAdmin->perm->revokeGroupRight($params);
            }
        } elseif ($p_value) {
            $LiveUserAdmin->perm->grantGroupRight($params);
        }
    } // fn setValue


	/**
     * Return an array of config values in the form array("varname" => "value");
     *
     * @return array
     */
    function getConfig()
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
    function hasPermission($p_permissionString)
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
	function setPermission($p_permissionString, $p_permissionValue)
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
    function GetByName($p_name)
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
    function GetUserTypeFromConfig($p_configVars)
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
    function GetUserTypes()
    {
        global $LiveUserAdmin;

        $userTypes = array();
        $res = $LiveUserAdmin->perm->getGroups();
        foreach ($res as $userType) {
            $tmpUserType =& new UserType($userType['group_id']);
            $userTypes[] = $tmpUserType;
        }
        return $userTypes;
    } // fn GetUserTypes

} // class UserType

?>