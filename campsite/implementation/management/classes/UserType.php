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
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */
class UserType {
	var $m_config = array();
	var $m_userTypeName = null;
	var $m_exists = false;
	
	/**
	 * @param string $p_userType
	 */
	function UserType($p_userType = null)
	{
		$this->m_userTypeName = $p_userType;
		if (!empty($p_userType)) {
			$this->fetch();
		}
	} // constructor

	
	/**
	 * Get the user type from the database.
	 * @return void
	 */
	function fetch() 
	{
		global $Campsite;
		$queryStr = 'SELECT varname, value FROM UserTypes '
					." WHERE user_type_name='".$this->m_userTypeName."'";
		$config = $Campsite['db']->GetAll($queryStr);
		if ($config) {
			// Make m_config an associative array.
			foreach ($config as $value) {
				$this->m_config[$value['varname']] = $value['value'];
			}
			$this->m_exists = true;
		}
	} // fn fetch


	function exists()
	{
		return $this->m_exists;
	} // fn exists
	
	
	/**
	 * Create the new UserType with the config variables given.
	 * If a config variable is not set, the default value will be used.
	 * 
	 * @param array $p_configVars
	 */
	function create($p_name, $p_configVars = null)
	{
		global $Campsite;
		if (empty($p_name) || !is_string($p_name)) {
			return false;
		}
		$this->m_userTypeName = $p_name;
		$defaultConfig = User::GetDefaultConfig();
		foreach ($defaultConfig as $varname => $value) {
			if (isset($p_configVars[$varname])) {
				$defaultConfig[$varname] = $p_configVars[$varname];
			}
		}
		foreach ($defaultConfig as $varname => $value) {
			$sql = "INSERT INTO UserTypes SET "
				   ." user_type_name='".mysql_real_escape_string($p_name)."', "
				   ." varname='$varname',"
				   ." value='$value'";
			$Campsite['db']->Execute($sql);
		}

		$this->fetch();
		if ($this->exists()) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('User type $1 added', $p_name);
			Log::Message($logtext, null, 121);			
		}
		return true;
	} // fn create
	

	/**
	 * Delete the user type.
	 *
	 */
	function delete()
	{
		global $Campsite;
		$query = "DELETE FROM UserTypes WHERE user_type_name='".mysql_real_escape_string($this->m_userTypeName)."'";
		if ($Campsite['db']->Execute($query)) {
			$this->m_exists = false;
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('User type $1 deleted', $this->m_userTypeName);
			Log::Message($logtext, null, 122);
			return true;
		}
		return false;
	} // fn delete
	
	
	/**
	 * Get the name of this user type.
	 * @return string
	 */
	function getName()
	{
		return $this->m_userTypeName;
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
	 * Note that this does not create a new config variable.
	 *
	 * @param string $p_varName
	 * @param mixed $p_value
	 * 
	 * @return void
	 */
	function setValue($p_varName, $p_value)
	{
		global $Campsite;
		if (isset($this->m_config[$p_varName]) && ($this->m_config[$p_varName] != $p_value) ) {
			$sql = "UPDATE UserTypes SET value='".mysql_real_escape_string($p_value)."'"
				   ." WHERE user_type_name='".$this->m_userTypeName."'"
				   ." AND varname='".mysql_real_escape_string($p_varName)."'";
			$Campsite['db']->Execute($sql);
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
		return (isset($this->m_config[$p_permissionString])
				&& ($this->m_config[$p_permissionString] == 'Y') );
	} // fn hasPermission
	
	
	/**
	 * Set the specified permission.
	 *
	 * @param string $p_permissionString
	 *
	 * @param boolean $p_permissionValue
	 *
	 */
	function setPermission($p_permissionString, $p_permissionValue)
	{
		$p_permissionValue = $p_permissionValue ? 'Y' : 'N';
		$this->setValue($p_permissionString, $p_permissionValue);
	} // fn setPermission
	
	
	/**
	 * Set the user type to staff or subscriber
	 *
	 * @param boolean $p_isAdmin
	 *
	 */
	function setAdmin($p_isAdmin)
	{
		$p_isAdmin = $p_isAdmin ? 'N' : 'Y';
		$this->setValue('Reader', $p_isAdmin);
	} // fn setAdmin
	
	
	/**
	 * @return boolean
	 */
	function isAdmin()
	{
		return ($this->getValue('Reader') == 'N');
	} // fn isAdmin
	
	
	/**
	 * Get the user type that matches the given config variables.
	 *
	 * @param array $p_configVars
	 * @return string
	 */
	function GetUserTypeFromConfig($p_configVars)
	{
		global $Campsite;
		if (!is_array($p_configVars) || (count($p_configVars) == 0) ) {
			return false;
		}
		$userTypes = UserType::GetUserTypes();
		
		$where = array();
		foreach ($p_configVars as $name => $value) {
			if (is_bool($value)) {
				$value = $value ? 'Y' : 'N';
			}
			$where[] = "(varname='".mysql_real_escape_string($name)."'"
					  ." AND value='".mysql_real_escape_string($value)."')";
		}
		$whereStr = implode(' OR ', $where);
		
		$totalConfigValues = count($p_configVars);
		$mismatches = array();
		foreach ($userTypes as $userType) {
			$queryStr = "SELECT COUNT(*) FROM UserTypes WHERE user_type_name='".$userType->getName()."'"
						." AND ($whereStr)";
			$numMatches = $Campsite['db']->GetOne($queryStr);
			
			// DEBUGGING CODE - DO NOT DELETE
			$queryStr = "SELECT * FROM UserTypes WHERE user_type_name='".$userType->getName()."'"
						." AND ($whereStr)";
			$rows = $Campsite['db']->GetAll($queryStr);
			foreach ($p_configVars as $varname => $value) {
				$found = false;
				foreach ($rows as $row) {
					if ($row['varname'] == $varname) {
						$found =true;
						break;
					}
				}
				if (!$found) {
					$mismatches[$userType->getName()][] = $varname;
				}
			}
			
			if ($numMatches >= $totalConfigValues) {
				return $userType;
			}
		}
		return false;
	} // fn GetUserTypeFromConfig


	/**
	 * Get all the user types with the exception of those with the Reader permission.
	 *
	 * @return array
	 * 		An array of UserType objects.
	 */
	function GetUserTypes()
	{
		global $Campsite;
		$userTypes = array();
		$res = $Campsite['db']->Execute("SELECT DISTINCT(user_type_name) as name FROM UserTypes");
		while ($row = $res->FetchRow()) {
			$tmpUserType =& new UserType($row['name']);
			$userTypes[] = $tmpUserType;
		}
		return $userTypes;
	} // fn GetUserTypes
		
} // class UserType

?>