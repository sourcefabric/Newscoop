<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

class Article extends DatabaseObject {
	/**
	 * Publication ID
	 * @var int
	 */
	var $IdPublication;
	
	/**
	 * Issue ID
	 * @var int
	 */
	var $NrIssue;
	
	/**
	 * Section ID
	 * @var int
	 */
	var $NrSection;
	
	/**
	 * Article ID
	 * @var int
	 */
	var $Number;
	
	/**
	 * Language ID
	 * @var int
	 */
	var $IdLanguage;
	
	/**
	 * Article Type
	 * @var string
	 */
	var $Type;
	
	/**
	 * User ID of user who created the article.
	 * @var int
	 */
	var $IdUser;
	
	/**
	 * Name is the title of the article.
	 * @var string
	 */
	var $Name;
	
	/**
	 * Whether the article is on the front page or not.
	 * This is represented as "N" or "Y".
	 * @var string
	 */
	var $OnFrontPage;
	
	/**
	 * Whether or not the article is on the section or not.
	 * This is represented as "N" or "Y".
	 * @var string
	 */
	var $OnSection;
	
	var $Published;
	var $UploadDate;	
	var $Keywords;
	var $Public;
	var $IsIndexed;
	var $LockUser;
	var $LockTime;
	var $ShortName;
	
	/**
	 * Map of database column names to internal variable names.
	 * @var array
	 */
	var $m_columnNames = array("IdPublication",
							   "NrIssue",
							   "NrSection",
							   "Number",
							   "IdLanguage",
							   "Type",
					 	  	   "Name",
					 	  	   "IdUser",
					  	  	   "OnFrontPage",
					 	  	   "OnSection",
					 	  	   "Published",
					 	  	   "UploadDate",
					 	  	   "Keywords",
					 	  	   "Public",
					 	  	   "IsIndexed",
					 	  	   "LockUser",
					 	  	   "LockTime",
					 	  	   "ShortName"
					 			);
	
	/**
	 * The column names used for the primary key.
	 * @var array
	 */
	var $m_primaryKeyColumnNames = array("IdPublication",
								   		 "NrIssue",
							   		 	 "NrSection",
							   		 	 "Number",
							   		 	 "IdLanguage");

	var $m_dbTableName = "Articles";
	
	/**
	 * Construct by passing in the primary key to access the article in 
	 * the database.
	 *
	 * @param int p_publicationId
	 *
	 * @param int p_issueId
	 *
	 * @param int p_sectionId
	 *
	 * @param int p_languageId
	 *
	 * @param int p_articleId
	 *		Not required when creating an article.
	 */
	function Article($p_publicationId, $p_issueId, $p_sectionId, 
					 $p_languageId, $p_articleId = null) 
	{
		$this->IdPublication = $p_publicationId;
		$this->NrIssue = $p_issueId;
		$this->NrSection = $p_sectionId;
		$this->IdLanguage = $p_languageId;
		$this->Number = $p_articleId;
		if (!is_null($p_articleId)) {
			$this->fetch();
		}
	} // ctor
	
	
	/**
	 * Create an article in the database.  Use the SET functions to
	 * change individual values.
	 *
	 * @param string p_name
	 * @param string p_shortName
	 * @param string p_articleType
	 *
	 * @return void
	 */
	function create($p_name, $p_shortName, $p_articleType) {
		global $Campsite;
		// Create the article ID.
		$queryStr = "UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)";
		$Campsite["db"]->Execute($queryStr);
		$this->Number = $Campsite["db"]->Insert_ID();
		
		// Create the record
		$values = array("Name" => $p_name, 
						"ShortName" => $p_shortName,
						"Type" => $p_articleType,
						"Public" => 'Y');
		parent::create($values);
		$queryStr = "UPDATE ".$this->m_dbTableName
					. " SET UploadDate=NOW()"
					. " WHERE " . $this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
		
		$this->fetch();
	
		// Added by sebastian
		// Paul asks: what does this do? 
		//if (function_exists ("incModFile")) {
		//	incModFile ();
		//}

		// Insert an entry into the article type table.
		$articleData =& new ArticleType($this->Type, $this->Number, $this->IdLanguage);
		$articleData->create();
	} // fn create

	
	/**
	 *
	 */
	function delete() {
		// Delete row from article type table.
		$articleData =& new ArticleType($this->Type, $this->Number, $this->IdLanguage);
		$articleData->delete();
		
		// Delete row from Articles table.
		parent::delete();
	} // fn delete
	
	
	/**
	 * Get the name of the dynamic article type table.
	 *
	 * @return string
	 */
	function getArticleTypeTableName() {
		return "X".$this->Type;
	} // fn getArticleTypeTableName
	
	
	/**
	 * @return int
	 */
	function getPublicationId() {
		return $this->IdPublication;
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getIssueId() {
		return $this->NrIssue;
	} // fn getIssueId
	
	
	/**
	 * @return int
	 */
	function getSectionId() {
		return $this->NrSection;
	} // fn getSectionId
	
	
	/**
	 * @return int
	 */
	function getLanguageId() {
		return $this->IdLanguage;
	} // fn getLanguageId
	
	
	/**
	 * @return int
	 */ 
	function getArticleId() {
		return $this->Number;
	} // fn getArticleId
	
	
	/**
	 * @return string
	 */
	function getTitle() {
		return $this->Name;
	} // fn getTitle
	
	
	/**
	 * Set the title of the article.
	 *
	 * @param string title
	 *
	 * @return void
	 */
	function setTitle($p_title) {
		parent::setProperty("Name", $p_title);
	} // fn setTitle

	
	/**
	 * Get the article type.
	 * @return string
	 */
	function getType() {
		return $this->Type;
	} // fn getType
	

	/**
	 * Set the article type.
	 * @param string value
	 */
//	function setType($value) {
//		parent::setProperty("Type", $value);
//	} // fn setType
	
	
	/**
	 * Return the user ID of the user who created this article.
	 * @return int
	 */
	function getUserId() {
		return $this->IdUser;
	} // fn getUserId
	
	
	/**
	 * @param int value
	 */
	function setUserId($value) {
		parent::setProperty("IdUser", $value);
	}
	
	
	/**
	 * Return true if the article is on the front page.
	 * @return boolean
	 */
	function onFrontPage() {
		return ($this->OnFrontPage == 'Y');
	} // fn onFrontPage
	
	
	/**
	 * @param boolean value
	 */
	function setOnFrontPage($value) {
		parent::setProperty("OnFrontPage", $value?"Y":"N");
	} // fn setOnFrontPage
	
	
	/**
	 * @return boolean
	 */
	function onSection() {
		return ($this->OnSection == 'Y');
	} // fn onSection
	
	
	/**
	 * @param boolean value
	 */
	function setOnSection($value) {
		parent::setProperty("OnSection", $value?"Y":"N");
	} // fn setOnSection
	
	
	/**
	 * @return string
	 * 		Can be "Y", "S", or "N".
	 */
	function getPublished() {
		return $this->Published;
	} // fn isPublished
	
	
	/**
	 * Set the published state of the article.  
	 * Can be "Y", "S", or "N".
	 * @param string value
	 */
	function setPublished($value) {
		parent::setProperty("Published", $value);
	} // fn setIsPublished
	
	
	/**
	 * Return the date the article was created in the form YYYY-MM-DD.
	 * @return string
	 */
	function getUploadDate() {
		return $this->UploadDate;
	} // fn getUploadDate
	
	
	/**
	 * @param date value
	 */ 
//	function setUploadDate($value) {
//		parent::setProperty("UploadDate", $value);
//	} // fn setUploadDate
	
	
	/**
	 * @return string
	 */
	function getKeywords() {
		return $this->Keywords;
	} // fn getKeywords
	
	
	/**
	 * @param string $value
	 */
	function setKeywords($value) {
		parent::setProperty("Keywords", $value);
	} // fn setKeywords
	
	
	/**
	 * @return boolean
	 */
	function isPublic() {
		return ($this->Public == 'Y');
	} // fn isPublic
	
	
	/**
	 *
	 * @param boolean value
	 */
	function setIsPublic($value) {
		parent::setProperty("Public", $value?"Y":"N");
	} // fn setIsPublic
	
	
	/**
	 * @return boolean
	 */
	function isIndexed() {
		return ($this->IsIndexed == 'Y');
	} // fn isIndexed
	
	
	/**
	 * @param boolean value
	 */
	function setIsIndexed($value) {
		parent::setProperty("IsIndexed", $value?"Y":"N");
	} // fn setIsIndexed
	
	
	/**
	 * Return the user ID of the user who has locked the article.
	 * @return int
	 */
	function getLockedByUser() {
		return $this->LockUser;
	} // fn getLockedByUser
	
	
	/**
	 * @param int value
	 */
	function setLockedByUser($value) {
		parent::setProperty("LockUser", $value);
	} // fn setLockedByUser
	
	
	/**
	 * @return string
	 *		In the form of YYYY-MM-DD HH:MM:SS
	 */
	function getLockTime() {
		return $this->LockTime;
	} // fn getLockTime

	
	/**
	 *
	 */
	function setLockTime() {
		// TODO
	} // fn setLockTime
	
	/**
	 * @return string
	 */
	function getShortName() {
		return $this->ShortName;
	} // fn getShortName
	
	
	/**
	 * @param string value
	 */
	function setShortName($value) {
		parent::setProperty("ShortName", $value);
	} // fn setShortName
	
	
	function getLanguages() {
		global $Campsite;
	 	$queryStr = "SELECT IdLanguage FROM Articles "
	 				." WHERE IdPublication=".$this->IdPublication
	 				." AND NrIssue=".$this->NrIssue
	 				." AND NrSection=".$this->NrSection
	 				." AND Number=".$this->Number;
	 	$languageIds = $Campsite["db"]->GetCol($queryStr);
	 	$languages = array();
		foreach ($languageIds as $languageId) {
			$languages[] =& new Language($languageId);
		}
		return $languages;
	} // fn getLanguages
	
	
	function getArticleTypeObject() {
		return new ArticleType($this->Type, $this->Number, $this->IdLanguage);
	} // fn getArticleTypeObject

	
	function lock($p_userId) {
		global $Campsite;
		$queryStr = "UPDATE Articles "
					." SET LockUser=".$p_userId
					.", LockTime=NOW() "
					." WHERE ". $this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
	} // fn lock

} // class Article

?>