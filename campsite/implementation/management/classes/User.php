<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class User extends DatabaseObject {
	var $m_dbTableName = "Users";
	var $m_primaryKeyColumnNames = array("Id");
	var $m_columnNames = array("Id", 
							   "KeyId",
							   "Name",
							   "UName",
							   "Password",
							   "Email",
							   "Reader",
							   "City",
							   "StrAddress",
							   "State",
							   "CountryCode",
							   "Phone",
							   "Fax",
							   "Contact",
							   "Phone2",
							   "Title",
							   "Gender",
							   "Age",
							   "PostalCode",
							   "Employer",
							   "EmployerType",
							   "Position",
							   "Interests",
							   "How",
							   "Languages",
							   "Improvements",
							   "Pref1",
							   "Pref2",
							   "Pref3",
							   "Pref4",
							   "Field1",
							   "Field2",
							   "Field3",
							   "Field4",
							   "Field5",
							   "Text1",
							   "Text2",
							   "Text3");
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
		$this->Id = $p_userId;
		if (!is_null($p_userId) && ($p_userId > 0)) {
			$this->fetch();
		}
	} // constructor
	
	function getName() {
		return $this->Name;
	} // fn getName
	
	function getUName() {
		return $this->UName;
	} // fn getUName
	
} // class User

?>