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
		'time_created');

	var $m_defaultConfig = array(
		'ManagePub'=>'N',
		'DeletePub'=>'N',
		'ManageIssue'=>'N',
		'DeleteIssue'=>'N',
		'ManageSection'=>'N',
		'DeleteSection'=>'N',
		'AddArticle'=>'N',
		'ChangeArticle'=>'N',
		'MoveArticle'=>'N',
		'TranslateArticle'=>'N',
		'DeleteArticle'=>'N',
		'AttachImageToArticle'=>'N',
		'AttachTopicToArticle'=>'N',
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
		'EditorFindReplace'=>'N',
		'EditorCharacterMap'=>'N',
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
		'ChangeSystemPreferences'=>'N',
		'AddFile'=>'N',
		'ChangeFile'=>'N',
		'DeleteFile'=>'N',
		'CommentModerate'=>'N',
		'CommentEnable'=>'N',
		'SyncPhorumUsers'=>'N');

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
		if (is_array($p_values)) {
			$p_values['time_created'] = strftime("%Y-%m-%d %H:%M:%S", time());
		}
		$success = parent::create($p_values);
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
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
		global $g_ado_db;
		if ($this->exists()) {
			parent::delete();
			$g_ado_db->Execute("DELETE FROM UserConfig WHERE fk_user_id = ".$this->m_data['Id']);
			$res = $g_ado_db->Execute("SELECT Id FROM Subscriptions WHERE IdUser = ".$this->m_data['Id']);
			while ($row = $res->FetchRow()) {
				$g_ado_db->Execute("DELETE FROM SubsSections WHERE IdSubscription=".$row['Id']);
			}
			$g_ado_db->Execute("DELETE FROM Subscriptions WHERE IdUser=".$this->m_data['Id']);
			$g_ado_db->Execute("DELETE FROM SubsByIP WHERE IdUser=".$this->m_data['Id']);
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
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
		global $g_ado_db;
		$success = parent::fetch($p_recordSet);
		if ($success) {
			// Fetch the user's permissions.
			$queryStr = 'SELECT varname, value FROM UserConfig '
						.' WHERE fk_user_id='.$this->m_data['Id'];
			$config = $g_ado_db->GetAll($queryStr);
			if ($config) {
				// Make m_config an associative array.
				foreach ($config as $value) {
					$this->m_config[$value['varname']] = $value['value'];
				}
			}
		}
	} // fn fetch


	function FetchUserByName($p_username, $p_adminOnly = false)
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM Users WHERE UName='$p_username'";
		if ($p_adminOnly) {
			$queryStr .= " AND Reader='N'";
		}
		$row = $g_ado_db->GetRow($queryStr);
		if ($row) {
			$user =& new User();
			$user->fetch($row);
			return $user;
		}
		return null;
	} // fn FetchUserByName


	/**
	 * Return the user type if there is one, or null if not.
	 *
	 * @return string
	 */
	function getUserType()
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
	function setUserType($p_userType)
	{
		global $g_ado_db;

		if (!$this->exists()) {
			return;
		}

		// Fetch the user type's permissions.
		$userType =& new UserType($p_userType);
		if ($userType->exists()) {
			$configVars = $userType->getConfig();
			foreach ($configVars as $varname => $value) {
				$queryStr = "SELECT value FROM UserConfig "
							." WHERE fk_user_id=".$this->m_data['Id']
							." AND varname='$varname'";
				$exists = $g_ado_db->GetOne($queryStr);
				if ($exists !== false) {
					if ($value != $this->m_config[$varname]) {
						$queryStr = "UPDATE UserConfig SET value='$value' "
									." WHERE fk_user_id=".$this->m_data['Id']
									." AND varname='$varname'";
						$g_ado_db->Execute($queryStr);
					}
				} else {
					$queryStr = "INSERT INTO UserConfig SET "
								." fk_user_id=".$this->m_data['Id'].","
								." varname='$varname',"
								." value='$value'";
					$g_ado_db->Execute($queryStr);
				}
			}
			// Update the user type in the user table.
			$this->setProperty('fk_user_type', $p_userType);
			$this->fetch();

			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('User permissions for $1 changed',
							 $this->m_data['Name']
							 ." (".$this->m_data['UName'].")");
			Log::Message($logtext, null, 55);
		}
	} // fn setUserType


	/**
	 * @return int
	 */
	function getUserId()
	{
		return $this->m_data['Id'];
	} // fn getUserId


	/**
	 * Get unique login key for this user - login key is only good for the
	 * time the user is logged in.
	 * @return int
	 */
	function getKeyId()
	{
		return $this->m_data['KeyId'];
	} // fn getKeyId


	/**
	 * Get the real name of the user.
	 * @return string
	 */
	function getRealName()
	{
		return $this->m_data['Name'];
	} // fn getRealName


	/**
	 * Get the login name of the user.
	 * @return string
	 */
	function getUserName()
	{
		return $this->m_data['UName'];
	} // fn getUserName


	/**
	 * Get the encrypted password.
	 *
	 * @return string
	 */
	function getPassword()
	{
		return $this->m_data['Password'];
	} // fn getPassword


	/**
	 * Get the email address of the user.
	 *
	 * @return string
	 */
	function getEmail()
	{
	    return $this->m_data['EMail'];
	} // fn getEmail


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
		global $g_ado_db;
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
					$g_ado_db->Execute($sql);
					$this->m_config[$p_varName] = $p_value;
				}
			} else {
				$sql = "INSERT INTO UserConfig SET "
					   ." fk_user_id=".$this->m_data['Id'].", "
					   ." varname='".mysql_real_escape_string($p_varName)."', "
					   ." value='".mysql_real_escape_string($p_value)."'";
				$g_ado_db->Execute($sql);
				$this->m_config[$p_varName] = $p_value;
			}

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
		if (isset($this) && isset($this->m_defaultConfig)) {
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


	function updatePermissions($p_permissions)
	{
		global $g_ado_db;
		$config = array_keys($this->m_config);
		$updateArray = array();
		foreach ($p_permissions as $permission => $value) {
			if (in_array($permission, $config)) {
				$value = $value ? 'Y' : 'N';
				if ($value != $this->m_config[$permission]) {
					$sql = "UPDATE UserConfig SET value='$value' "
						   ." WHERE fk_user_id=".$this->m_data['Id']
						   ." AND varname='$permission'";
					$g_ado_db->Execute($sql);
					$this->m_config[$permission] = $value;
				}
			}
		}
		$userType = UserType::GetUserTypeFromConfig($this->m_config);
		if ($userType) {
			$this->setProperty('fk_user_type', $userType->getName());
		} else {
			$this->setProperty('fk_user_type', 'NULL', true, true);
		}
	} // fn updatePermissions


	/**
	 * Return TRUE if this user is an administrator.
	 *
	 * @return boolean
	 */
	function isAdmin()
	{
		return ($this->m_data['Reader'] == 'N');
	} // fn isAdmin


	/**
	 * Check if the password is a valid password in the old format.
	 *
	 * @return boolean
	 */
	function isValidOldPassword($p_password)
	{
		global $g_ado_db;
		$userPasswordSQL = mysql_real_escape_string($p_password);
        $queryStr = "SELECT PASSWORD('$userPasswordSQL') AS old_password_1, "
        			." OLD_PASSWORD('$userPasswordSQL') AS old_password_2"
        			." FROM Users "
					." WHERE Id = '".$this->m_data['Id']."' ";
		if (!($row = $g_ado_db->GetRow($queryStr))) {
			return false;
		}
		// Check if the given password matches the one in the database
		if ( ($this->m_data['Password'] == $row['old_password_1'])
			|| ($this->m_data['Password'] == $row['old_password_2'] ) ) {
			return true;
		}
		return false;
	} // fn isValidOldPassword


	/**
	 * Return TRUE if the given password matches the one in the database.
	 *
	 * @param string $p_password
	 * @param boolean $p_isEncrypted
	 * 		Set to true if the password is already encrypted in SHA1 format.
	 * @return boolean
	 */
	function isValidPassword($p_password, $p_isEncrypted = false)
	{
		global $g_ado_db;
		if (!$p_isEncrypted) {
			$userPasswordSQL = mysql_real_escape_string($p_password);
    	    $queryStr = "SELECT SHA1('$userPasswordSQL') as encrypted_password FROM Users "
						. " WHERE Id = '".$this->m_data['Id']."' ";
			$encryptedPassword = $g_ado_db->GetOne($queryStr);
			return ($encryptedPassword == $this->getPassword());
		}
		return ($p_password == $this->m_data['Password']);
	} // fn isValidPassword


	/**
	 * @param string $p_password
	 * @return void
	 */
	function setPassword($p_password)
	{
		global $g_ado_db;
		$queryStr = "SELECT SHA1('".mysql_real_escape_string($p_password)."') AS PWD";
		$row = $g_ado_db->GetRow($queryStr);
		$this->setProperty('Password', $row['PWD']);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Password changed for $1', $this->m_data['Name']." (".$this->m_data['UName'].")");
		Log::Message($logtext, null, 54);
	}  // fn setPassword


	/**
	 * Initialize the per-session login key.  This makes sure the user can only
	 * login from one location at a time.
	 *
	 * @return void
	 */
	function initLoginKey()
	{
		// Generate the Key ID
		$this->setProperty('KeyId', 'RAND()*1000000000+RAND()*1000000+RAND()*1000', true, true);
	} // fn initLoginKey


	/**
	 * Return true if the user name exists.
	 *
	 * @param string $p_userName
	 * @return boolean
	 */
	function UserNameExists($p_userName)
	{
		global $g_ado_db;
		$sql = "SELECT UName FROM Users WHERE UName='".mysql_real_escape_string($p_userName)."'";
		if ($g_ado_db->GetOne($sql)) {
			return true;
		} else {
			return false;
		}
	} // fn UserNameExists


	/**
	 * Get all users matching the given parameters.
	 *
	 * @param boolean $p_onlyAdmin
	 * @param string $p_userType
	 * @return array
	 */
	function GetUsers($p_onlyAdmin = true, $p_userType = null)
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
		$sql = "SELECT * FROM Users " . $whereStr;
		return DbObjectArray::Create("User", $sql);
	} // fn GetUsers


	/**
	 * Sync campsite and phorum users.
	 */
	function syncPhorumUser()
	{
		$phorumUser = Phorum_user::GetByUserName($this->m_data['UName']);
		if ($phorumUser->setPassword($this->m_data['Password']) &&
			$phorumUser->setEmail($this->m_data['EMail'])) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Base data synchronized to phorum user for $1', $this->m_data['Name']." (".$this->m_data['UName'].")");
			Log::Message($logtext, null, 161);
		}
	} // fn syncPhorumUser

} // class User

?>
