<?
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class UserPerm {
	var $IdUser;
	
	/**
	 * @var string
	 */
	var $m_dbTableName = "UserPerm";
	
	/**
	 * @var array
	 */
	var $m_primaryKeyColumnNames = array("IdUser");
	
	var $m_columnNames = array(
							"ManagePub",
							"DeletePub",
							"ManageIssue",
							"DeleteIssue",
							"ManageSection",
							"DeleteSection",
							"AddArticle",
							"ChangeArticle",
							"DeleteArticle",
							"AddImage",
							"ChangeImage",
							"DeleteImage",
							"ManageTempl",
							"DeleteTempl",
							"ManageUsers",
							"ManageSubscriptions",
							"DeleteUsers",
							"ManageUserTypes",
							"ManageArticleTypes",
							"DeleteArticleTypes",
							"ManageLanguages",
							"DeleteLanguages",
							"ManageDictionary",
							"DeleteDictionary",
							"ManageCountries",
							"DeleteCountries",
							"ManageClasses",
							"MailNotify",
							"ViewLogs",
							"ManageLocalizer",
							"Publish",
							"ManageTopics"
						);
	
	
	var $ManagePub = "N";
	var $DeletePub = "N";
	var $ManageIssue = "N";
	var $DeleteIssue = "N";
	var $ManageSection = "N";
	var $DeleteSection = "N";
	var $AddArticle = "N";
	var $ChangeArticle = "N";
	var $DeleteArticle = "N";
	var $AddImage = "N";
	var $ChangeImage = "N";
	var $DeleteImage = "N";
	var $ManageTempl = "N";
	var $DeleteTempl = "N";
	var $ManageUsers = "N";
	var $ManageSubscriptions = "N";
	var $DeleteUsers = "N";
	var $ManageUserTypes = "N";
	var $ManageArticleTypes = "N";
	var $DeleteArticleTypes = "N";
	var $ManageLanguages = "N";
	var $DeleteLanguages = "N";
	var $ManageDictionary = "N";
	var $DeleteDictionary = "N";
	var $ManageCountries = "N";
	var $DeleteCountries = "N";
	var $ManageClasses = "N";
	var $MailNotify = "N";
	var $ViewLogs = "N";
	var $ManageLocalizer = "N";
	var $Publish = "N";
	var $ManageTopics = "N";
	
	function UserPerm($p_userId, $p_userKey) {
		$this->IdUser = $p_userId;
	} // constructor
	
	function hasPerm($p_perm) {
		return ($this->$p_perm == 'Y');
	} // fn hasPerm
	
} // class UserPerm

?>