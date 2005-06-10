<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

/**
 * @package Campsite
 */
class User extends DatabaseObject {
	var $m_dbTableName = 'Users';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_permissions = array();
	var $m_columnNames = array(
		'Id',
		'KeyId',
		'Name',
		'UName',
		'Password',
		'EMail',
		'Reader',
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
		'Text3');

	/**
	 * @param int $p_userId
	 */
	function User($p_userId = null) 
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
	 * @param array $p_recordSet
	 */
	function fetch($p_recordSet = null) 
	{
		global $Campsite;
		parent::fetch($p_recordSet);
		// Fetch the user's permissions.
		$queryStr = 'SELECT * FROM UserPerm '
					.' WHERE IdUser='.$this->getProperty('Id');
		$permissions = $Campsite['db']->GetRow($queryStr);
		if ($permissions) {
			// Make m_permissions a boolean array.
			foreach ($permissions as $key => $value) {
				$this->m_permissions[$key] = ($value == 'Y');
			}
		}
	} // fn fetch


	/**
	 * @param string $p_userType
	 */
	function setUserType($p_userType)
	{
		global $Campsite;
		// Fetch the user type's permissions.
		$queryStr = "SELECT * FROM UserTypes WHERE Name = '"
			.mysql_real_escape_string($p_userType)."'";
		$permissions = $Campsite['db']->GetRow($queryStr);
		if ($permissions) {
			// Make m_permissions a boolean array.
			foreach ($permissions as $key => $value) {
				$this->m_permissions[$key] = ($value == 'Y');
				if ($key != 'Name' && $key != 'Reader')
					$values .= ", '" . mysql_real_escape_string($value) . "'";
			}
			if ($this->exists()) {
				$queryStr = "INSERT IGNORE INTO UserPerm VALUES(".$this->getId()."$values)";
				$Campsite['db']->Query($queryStr);
			}
		}
	} // fn setUserType
	
	
	/**
	 * @return int
	 */
	function getId() 
	{
		return $this->getProperty('Id');
	} // fn getId
	
	
	/**
	 *
	 * @return int
	 */
	function getKeyId() 
	{
		return $this->getProperty('KeyId');
	} // fn getKeyId
	
	
	/**
	 * Get the real name of the user.
	 * @return string
	 */
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * Get the login name of the user.
	 * @return string
	 */
	function getUserName() 
	{
		return $this->getProperty('UName');
	} // fn getUName

	
	/**
	 * Return true if the user has the permission specified.
	 * The database column names from the table UserPerm are used
	 * as the permission strings.
	 *
	 * @param string $p_permissionString
	 *
	 * @return boolean
	 */
	function hasPermission($p_permissionString) 
	{
		return (isset($this->m_permissions[$p_permissionString])
				&& $this->m_permissions[$p_permissionString]);
	} // fn hasPermission
	
	
	/**
	 * @return boolean
	 */
	function isAdmin() 
	{
		return (count($this->m_permissions) > 0);
	} // fn isAdmin


	/**
	 * @return boolean
	 */
	function isValidPassword($p_password) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM Users '
				. " WHERE Id = '".mysql_real_escape_string($this->getId())."' "
				. " AND Password = PASSWORD('".mysql_real_escape_string($p_password)."')";
		$row = $Campsite['db']->GetRow($queryStr);
		if ($row)
			return true;
		return false;
	}
	
	
	/**
	 * @return boolean
	 */
	function setPassword($p_password) 
	{
		global $Campsite;
		$queryStr = "SELECT PASSWORD('".mysql_real_escape_string($p_password)."') AS PWD";
		$row = $Campsite['db']->GetRow($queryStr);
		$this->setProperty('Password', $row['PWD']);
	}

	
	/**
	 * This is a static function.  Check if the user is allowed
	 * to access the site.
	 *
	 * @return array
	 * 		An array of two elements: 
	 *		boolean - whether the login was successful
	 *		object - if successful, the user object
	 */
	function Login($p_userName, $p_userPassword) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM Users '
					." WHERE UName='$p_userName' "
					." AND Password=PASSWORD('$p_userPassword') "
					." AND Reader='N'";
		$row = $Campsite['db']->GetRow($queryStr);
		if ($row) {
			// Generate the Key ID
			$user =& new User();
			$user->fetch($row);
			$user->setProperty('KeyId', 'RAND()*1000000000+RAND()*1000000+RAND()*1000', true, true);
			return array(true, $user);
		}
		else {
			return array(false, null);
		}
	} // fn Login
} // class User

?>