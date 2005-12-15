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
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */
class User extends DatabaseObject {
	var $m_dbTableName = 'Users';
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

	var $m_defaultConfig = array(
		'ManagePub'=>'N', 
		'DeletePub'=>'N', 
		'ManageIssue'=>'N', 
		'DeleteIssue'=>'N',
		'ManageSection'=>'N', 
		'DeleteSection'=>'N', 
		'AddArticle'=>'N', 
		'ChangeArticle'=>'N',
		'DeleteArticle'=>'N', 
		'AddImage'=>'N', 
		'ChangeImage'=>'N', 
		'DeleteImage'=>'N',
		'ManageTempl'=>'N', 
		'DeleteTempl'=>'N', 
		'ManageUsers'=>'N', 
		'ManageReaders'=>'N',
		'ManageSubscriptions'=>'N', 
		'DeleteUsers'=>'N', 
		'ManageUserTypes'=>'N', 
		'ManageArticleTypes'=>'N',
		'DeleteArticleTypes'=>'N', 
		'ManageLanguages'=>'N', 
		'DeleteLanguages'=>'N', 
		'MailNotify'=>'N',
		'ManageCountries'=>'N', 
		'DeleteCountries'=>'N', 
		'ViewLogs'=>'N', 
		'ManageLocalizer'=>'N',
		'ManageIndexer'=>'N', 
		'Publish'=>'N', 
		'ManageTopics'=>'N', 
		'EditorBold'=>'N', 
		'EditorItalic'=>'N',
		'EditorUnderline'=>'N', 
		'EditorUndoRedo'=>'N', 
		'EditorCopyCutPaste'=>'N', 
		'EditorImage'=>'N',
		'EditorTextAlignment'=>'N', 
		'EditorFontColor'=>'N', 
		'EditorFontSize'=>'N', 
		'EditorFontFace'=>'N',
		'EditorTable'=>'N', 
		'EditorSuperscript'=>'N', 
		'EditorSubscript'=>'N', 
		'EditorStrikethrough'=>'N',
		'EditorIndent'=>'N', 
		'EditorListBullet'=>'N', 
		'EditorListNumber'=>'N', 
		'EditorHorizontalRule'=>'N',
		'EditorSourceView'=>'N', 
		'EditorEnlarge'=>'N', 
		'EditorTextDirection'=>'N', 
		'EditorLink'=>'N',
		'EditorSubhead'=>'N',
		'InitializeTemplateEngine'=>'N',
		'AddFile'=>'N',
		'ChangeFile'=>'N',
		'DeleteFile'=>'N');		
		
	/**
	 * A user of the system is a frontend reader or a 'admin' user, meaning
	 * they have login rights to the backend.
	 * 
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
	
	
	function create($p_values = null)
	{
		$success = parent::create($p_values);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('User account $1 created', $this->m_data['Name']." (".$this->m_data['UName'].")");
			Log::Message($logtext, null, 51);		
		}
		return $success;
	} // fn create
	
	
	/**
	 * Delete the user.  This will delete all config values and subscriptions of the user.
	 *
	 * @return boolean
	 */
	function delete()
	{
		global $Campsite;
		if ($this->exists()) {
			parent::delete();
			$Campsite['db']->Execute("DELETE FROM UserConfig WHERE fk_user_id = ".$this->m_data['Id']);
			$res = $Campsite['db']->Execute("SELECT Id FROM Subscriptions WHERE IdUser = ".$this->m_data['Id']);
			while ($row = $res->FetchRow()) {
				$Campsite['db']->Execute("DELETE FROM SubsSections WHERE IdSubscription=".$row['Id']);
			}
			$Campsite['db']->Execute("DELETE FROM Subscriptions WHERE IdUser=".$this->m_data['Id']);
			$Campsite['db']->Execute("DELETE FROM SubsByIP WHERE IdUser=".$this->m_data['Id']);
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('The user account $1 has been deleted.', $this->m_data['Name']." (".$this->m_data['UName'].")");
			Log::Message($logtext, null, 52);
		}
		return true;
	} // fn delete
	
	
	/**
	 * Get the user from the database.
	 * 
	 * @param array $p_recordSet
	 */
	function fetch($p_recordSet = null) 
	{
		global $Campsite;
		$success = parent::fetch($p_recordSet);
		if ($success) {
			// Fetch the user's permissions.
			$queryStr = 'SELECT varname, value FROM UserConfig '
						.' WHERE fk_user_id='.$this->getProperty('Id');
			$config = $Campsite['db']->GetAll($queryStr);
			if ($config) {
				// Make m_config an associative array.
				foreach ($config as $value) {
					$this->m_config[$value['varname']] = $value['value'];
				}
			}
		}
	} // fn fetch


	/**
	 * Set the user to the given user type.
	 * 
	 * @param string $p_userType
	 * 
	 * @return void
	 */
	function setUserType($p_userType)
	{
		global $Campsite;
		
		if (!$this->exists()) {
			return;
		}
		
		// Fetch the user type's permissions.
		$userType =& new UserType($p_userType);
		if ($userType->exists()) {
			// Drop all current user permissions.
			//$queryStr = "DELETE FROM UserConfig WHERE fk_user_id=".$this->m_data['Id'];
			$configVars = $userType->getConfig();
			foreach ($configVars as $varname => $value) {
				$queryStr = "SELECT value FROM UserConfig "
							." WHERE fk_user_id=".$this->m_data['Id']
							." AND varname='$varname'";
				$exists = $Campsite['db']->GetOne($queryStr);
				if ($exists !== false) {
					if ($value != $this->m_config[$varname]) {
						$queryStr = "UPDATE UserConfig SET value='$value' "
									." WHERE fk_user_id=".$this->m_data['Id']
									." AND varname='$varname'";
						$Campsite['db']->Execute($queryStr);
					}
				} else {
					$queryStr = "INSERT INTO UserConfig SET "
								." fk_user_id=".$this->m_data['Id'].","
								." varname='$varname',"
								." value='$value'";
					$Campsite['db']->Execute($queryStr);
				}
			}
			$this->fetch();
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('User permissions for $1 changed', $this->m_data['Name']." (".$this->m_data['UName'].")");
			Log::Message($logtext, null, 55);
		}
	} // fn setUserType
	
	
	/**
	 * @return int
	 */
	function getUserId() 
	{
		return $this->getProperty('Id');
	} // fn getUserId
	
	
	/**
	 * Get unique login key for this user - login key is only good for the time the
	 * user is logged in.
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
	function getRealName() 
	{
		return $this->getProperty('Name');
	} // fn getRealName
	
	
	/**
	 * Get the login name of the user.
	 * @return string
	 */
	function getUserName() 
	{
		return $this->getProperty('UName');
	} // fn getUserName

	
	/**
	 * Return the value of the given variable name.
	 * If the variable name does not exist, return null.
	 *
	 * @param string $p_varName
	 * @return mixed
	 */
	function getConfigValue($p_varName)
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
	 */
	function setConfigValue($p_varName, $p_value)
	{
		global $Campsite;
		if (!$this->exists() || empty($p_varName) || !is_string($p_varName)) {
			return;
		}
		
		if (strtolower($p_varName) == "reader") {
			// Special case for the "Reader" property.
			$this->setProperty("Reader", $p_value);
		} else {
			if (isset($this->m_config[$p_varName])) {
				if ($this->m_config[$p_varName] != $p_value) {
					$sql = "UPDATE UserConfig SET value='".mysql_real_escape_string($p_value)."'"
						   ." WHERE fk_user_id=".$this->m_data['Id']
						   ." AND varname='".mysql_real_escape_string($p_varName)."'";
					$Campsite['db']->Execute($sql);
				}
			} else {
				$sql = "INSERT INTO UserConfig SET "
					   ." fk_user_id=".$this->m_data['Id'].", "
					   ." varname='".mysql_real_escape_string($p_varName)."', "
					   ." value='".mysql_real_escape_string($p_value)."'";
				$Campsite['db']->Execute($sql);			
			}
		}
	} // fn setConfigValue	
	
	
	/**
	 * Get the user config variables in the form array("varname" => "value").
	 *
	 * @return array
	 */
	function getConfig()
	{
		return $this->m_config;
	} // fn getConfig
	
	
	/**
	 * Get the default config for all users.
	 *
	 * @return array
	 */
	function GetDefaultConfig()
	{
		if (isset($this->m_defaultConfig)) {
			return $this->m_defaultConfig;
		} else {
			$tmpUser =& new User();
			return $tmpUser->m_defaultConfig;
		}
	} // fn GetDefaultConfig
	
	
	/**
	 * Return true if the user has the permission specified.
	 *
	 * @param string $p_permissionString
	 *
	 * @return boolean
	 */
	function hasPermission($p_permissionString) 
	{
		return (isset($this->m_config[$p_permissionString])
				&& ($this->m_config[$p_permissionString] == 'Y'));
	} // fn hasPermission
	
	
	/**
	 * Set the specified permission enabled or disabled.
	 *
	 * @param string $p_permissionString
	 * @param boolean $p_value
	 * 
	 * @return void
	 */
	function setPermission($p_permissionString, $p_value)
	{
		$p_value = $p_value ? 'Y' : 'N';
		$this->setConfigValue($p_permissionString, $p_value);		
	} // fn setPermission
	
	
	/**
	 * Return TRUE if this user is an administrator.
	 * 
	 * @return boolean
	 */
	function isAdmin() 
	{
		return ($this->getProperty('Reader') == 'N');
	} // fn isAdmin


	/**
	 * @return boolean
	 */
	function isValidPassword($p_password) 
	{
		global $Campsite;
		$userPasswordSQL = mysql_real_escape_string($p_password);
		$queryStr = "SELECT Password, SHA1('$userPasswordSQL') AS SHA1Password,"
				. " PASSWORD('$userPasswordSQL') AS OLDPassword FROM Users "
				. " WHERE Id = '".mysql_real_escape_string($this->getUserId())."' ";
		if (!($row = $Campsite['db']->GetRow($queryStr))) {
			return false;
		}
		if ($row['Password'] == $row['SHA1Password'] || $row['Password'] == $row['OLDPassword']) {
			return true;
		}
		$queryStr = "SELECT Password, OLD_PASSWORD('$userPasswordSQL') AS OLDPassword FROM Users "
				. " WHERE Id = '".mysql_real_escape_string($this->getUserId())."' ";
		if (!($row = $Campsite['db']->GetRow($queryStr))) {
			return false;
		}
		if ($row['Password'] == $row['OLDPassword']) {
			return true;
		}
	} // fn isValidPassword
	
	
	/**
	 * @return boolean
	 */
	function setPassword($p_password) 
	{
		global $Campsite;
		$queryStr = "SELECT SHA1('".mysql_real_escape_string($p_password)."') AS PWD";
		$row = $Campsite['db']->GetRow($queryStr);
		$this->setProperty('Password', $row['PWD']);
		if (function_exists("camp_load_language")) { camp_load_language("api");	}
		$logtext = getGS('Password changed for $1', $this->m_data['Name']." (".$this->m_data['UName'].")");
		Log::Message($logtext, null, 54);	
	}  // fn setPassword

	
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
		$queryStr = "SELECT * FROM Users WHERE UName='$p_userName' AND Reader='N'";
		$row = $Campsite['db']->GetRow($queryStr);
		if ($row) {
			$user =& new User();
			$user->fetch($row);
			if ($user->isValidPassword($p_userPassword)) {
				// Generate the Key ID
				$user->setProperty('KeyId', 'RAND()*1000000000+RAND()*1000000+RAND()*1000', true, true);
				return array(true, $user);
			}
			return array(false, null);
		}
		else {
			return array(false, null);
		}
	} // fn Login
	
	
	/**
	 * Return true if the user name exists.
	 *
	 * @param string $p_userName
	 * @return boolean
	 */
	function UserNameExists($p_userName)
	{
		global $Campsite;
		$sql = "SELECT UName FROM Users WHERE UName='".mysql_real_escape_string($p_userName)."'";
		if ($Campsite['db']->GetOne($sql)) {
			return true;
		} else {
			return false;
		}
	} // fn UserNameExists
	
} // class User

?>