<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class User extends DatabaseObject {
	var $m_dbTableName = "Users";
	var $m_primaryKeyColumnNames = array("Id");
	var $Id;
	var $KeyId;
	var $Name;
	var $UName;
	var $Password;
	var $Email;
	var $Reader;
	var $City;
	var $StrAddress;
	var $State;
	var $CountryCode;
	var $Phone;
	var $Fax;
	var $Contact;
	var $Phone2;
	var $Title;
	var $Gender;
	var $Age;
	var $PostalCode;
	var $Employer;
	var $EmployerType;
	var $Position;
	var $Interests;
	var $How;
	var $Languages;
	var $Improvements;
	var $Pref1;
	var $Pref2;
	var $Pref3;
	var $Pref4;
	var $Field1;
	var $Field2;
	var $Field3;
	var $Field4;
	var $Field5;
	var $Text1;
	var $Text2;
	var $Text3;
	
	function User($p_userId = null) {
		parent::DatabaseObject();
		$this->Id = $p_userId;
		if (!is_null($p_userId) && ($p_userId > 0)) {
			$this->fetch();
		}
	} // constructor
	
	function getId() {
		return $this->Id;
	} // fn getId
	
	function getKeyId() {
		return $this->KeyId;
	} // fn getKeyId
	
	function getName() {
		return $this->Name;
	} // fn getName
	
	function getUName() {
		return $this->UName;
	} // fn getUName

	/**
	 * This is a static function.
	 */
	function login($p_userName, $p_userPassword) {
		global $Campsite;
		$queryStr = "SELECT Id FROM Users "
					." WHERE UName='$p_userName' "
					." AND Password=PASSWORD('$p_userPassword') "
					." AND Reader='N'";
		$row = $Campsite["db"]->GetRow($queryStr);
		if ($row) {
			// Generate the Key ID
			$queryStr2 = "UPDATE Users "
						." SET KeyId=RAND()*1000000000+RAND()*1000000+RAND()*1000"
						." WHERE Id=".$row["Id"];
			$Campsite["db"]->Execute($queryStr2);
			$user =& new User($row["Id"]);
			return array(true, $user);
		}
		else {
			return array(false, null);
		}
	} // fn login
} // class User

?>