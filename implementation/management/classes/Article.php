<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

class Article extends DatabaseObject {
	/**
	 * The column names used for the primary key.
	 * @var array
	 */
	var $m_keyColumnNames = array("IdPublication",
						   		  "NrIssue",
							   	  "NrSection",
							   	  "Number",
							   	  "IdLanguage");

	var $m_dbTableName = "Articles";
	
	var $m_columnNames = array(
		// int - Publication ID 
		"IdPublication", 
		
		// int -Issue ID
		"NrIssue", 
		
		// int - Section ID
		"NrSection", 

		// int - Article ID
		"Number", 

		// int - Language ID,
		"IdLanguage",

		// string - Article Type
		"Type",
	
		// int - User ID of user who created the article
		"IdUser",
	
		// string - The title of the article.
		"Name",
	
		// string
		// Whether the article is on the front page or not.
	  	// This is represented as "N" or "Y".
		"OnFrontPage",
	
		/**
		 * Whether or not the article is on the section or not.
		 * This is represented as "N" or "Y".
		 * @var string
		 */
		"OnSection",
		"Published",
		"UploadDate",
		"Keywords",
		"Public",
		"IsIndexed",
		"LockUser",
		"LockTime",
		"ShortName");
		
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
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data["IdPublication"] = $p_publicationId;
		$this->m_data["NrIssue"] = $p_issueId;
		$this->m_data["NrSection"] = $p_sectionId;
		$this->m_data["IdLanguage"] = $p_languageId;
		$this->m_data["Number"] = $p_articleId;
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
		if ($Campsite["db"]->Affected_Rows() <= 0) {
			// If we were not able to get an ID, dont try to create the article.
			return;
		}
		$this->m_data["Number"] = $Campsite["db"]->Insert_ID();
		
		// Create the record
		$values = array("Name" => $p_name, 
						"ShortName" => $p_shortName,
						"Type" => $p_articleType,
						"Public" => 'Y');
		$success = parent::create($values);
		if (!$success) {
			return;
		}
		$this->setProperty("UploadDate", "NOW()", true, true);
//		$queryStr = "UPDATE ".$this->m_dbTableName
//					. " SET UploadDate=NOW()"
//					. " WHERE " . $this->getKeyWhereClause();
//		$Campsite["db"]->Execute($queryStr);
		
		$this->fetch();
	
		// Added by sebastian
		// Paul asks: what does this do? 
		if (function_exists ("incModFile")) {
			incModFile ();
		}

		// Insert an entry into the article type table.
		$articleData =& new ArticleType($this->m_data["Type"], 
			$this->m_data["Number"], 
			$this->m_data["IdLanguage"]);
		$articleData->create();
	} // fn create

	
	/**
	 * Delete article from database.
	 */
	function delete() {
		// Delete row from article type table.
		$articleData =& new ArticleType($this->m_data["Type"], 
			$this->m_data["Number"], 
			$this->m_data["IdLanguage"]);
		$articleData->delete();
		
		// Delete row from Articles table.
		parent::delete();
	} // fn delete
	
	
	/**
	 * Lock the article with the given User ID.
	 *
	 * @param int p_userId
	 *
	 */
	function lock($p_userId) {
		global $Campsite;
		$queryStr = "UPDATE Articles "
					." SET LockUser=".$p_userId
					.", LockTime=NOW() "
					." WHERE ". $this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
	} // fn lock

//	/**
//	 * Create and return an array representation of an article for use in a template.
//	 * @return array
//	 */
//	function getTemplateVar() {
//		$templateVar = array();
//		$templateVar["publication_id"] = $this->IdPublication;
//		$templateVar["issue_id"] = $this->NrIssue;
//		$templateVar["section_id"] = $this->NrSection;
//		$templateVar["article_id"] = $this->Number;
//		$templateVar["language_id"] = $this->IdLanguage;
//		$templateVar["article_type"] = $this->Type;
//		$templateVar["user_id"] = $this->IdUser;
//		$templateVar["title"] = $this->Name;
//		$templateVar["on_front_page"] = $this->OnFrontPage;
//		$templateVar["on_section"] = $this->OnSection;
//		$templateVar["published"] = $this->Published;
//		$templateVar["upload_date"] = $this->UploadDate;
//		$templateVar["keywords"] = $this->Keywords;
//		$templateVar["is_public"] = $this->Public;
//		$templateVar["is_indexed"] = $this->IsIndexed;
//		$templateVar["locked_by_user"] = $this->LockUser;
//		$templateVar["lock_time"] = $this->LockTime;
//		$templateVar["short_name"] = $this->ShortName;
//		return $templateVar;
//	} // fn getTemplateVar


	/**
	 * Return an array of Langauge objects, one for each
	 * type of language the article is written in.
	 * TODO: change this to a function that returns the set of all 
	 * articles, one for each language.
	 */
	function getLanguages() {
		global $Campsite;
	 	$queryStr = "SELECT IdLanguage FROM Articles "
	 				." WHERE IdPublication=".$this->m_data["IdPublication"]
	 				." AND NrIssue=".$this->m_data["NrIssue"]
	 				." AND NrSection=".$this->m_data["NrSection"]
	 				." AND Number=".$this->m_data["Number"];
	 	$languageIds = $Campsite["db"]->GetCol($queryStr);
	 	$languages = array();
		foreach ($languageIds as $languageId) {
			$languages[] =& new Language($languageId);
		}
		return $languages;
	} // fn getLanguages
	
	
	/**
	 * @return array
	 */
	function getArticlesInSection($p_publicationId, $p_issueId, $p_sectionId, $p_languageId) {
		global $Campsite;
		$queryStr = "SELECT * FROM Articles"
					." WHERE IdPublication='".$p_publicationId."'"
					." AND NrIssue='".$p_issueId."'"
					." AND NrSection='".$p_sectionId."'"
					." AND IdLanguage='".$p_languageId."'";
		$query = $Campsite["db"]->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article($row["IdPublication"], $row["NrIssue"],
				$row["NrSection"], $row["IdLanguage"]);
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		return $articles;
	} // fn getArticlesInSection
	
	
	/**
	 * Get the section that this article is in.
	 * @return object
	 */
	function getSection() {
		global $Campsite;
	    $queryStr = "SELECT * FROM Sections "
	    			." WHERE IdPublication=".$this->getPublicationId()
	    			." AND NrIssue=".$this->getIssueId()
	    			." AND IdLanguage=".$this->getLanguageId();
		$query = $Campsite["db"]->Execute($queryStr);
		if ($query->RecordCount() <= 0) {
			$queryStr = "SELECT * FROM Sections "
						." WHERE IdPublication=".$this->getPublicationId()
						." AND NrIssue=".$this->getIssueId()
						." LIMIT 1";
			$query = $Campsite["db"]->Execute($queryStr);	
		}
		$row = $query->FetchRow();
		$section =& new Section($this->getPublicationId(), $this->getIssueId(),
			$this->getLanguageId());
		$section->fetch($row);
	    return $section;
	} // fn getSection
	
	
	/**
	 * Get the name of the dynamic article type table.
	 *
	 * @return string
	 */
	function getArticleTypeTableName() {
		return "X".$this->m_data["Type"];
	} // fn getArticleTypeTableName
	
	
	/**
	 * @return int
	 */
	function getPublicationId() {
		return $this->getProperty("IdPublication");
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getIssueId() {
		return $this->getProperty("NrIssue");
	} // fn getIssueId
	
	
	/**
	 * @return int
	 */
	function getSectionId() {
		return $this->getProperty("NrSection");
	} // fn getSectionId
	
	
	/**
	 * @return int
	 */
	function getLanguageId() {
		return $this->getProperty("IdLanguage");
	} // fn getLanguageId
	
	
	/**
	 * @return int
	 */ 
	function getArticleId() {
		return $this->getProperty("Number");
	} // fn getArticleId
	
	
	/**
	 * @return string
	 */
	function getTitle() {
		return $this->getProperty("Name");
	} // fn getTitle
	
	
	/**
	 * Set the title of the article.
	 *
	 * @param string title
	 *
	 * @return void
	 */
	function setTitle($p_title) {
		return parent::setProperty("Name", $p_title);
	} // fn setTitle

	
	/**
	 * Get the article type.
	 * @return string
	 */
	function getType() {
		return $this->getProperty("Type");
	} // fn getType
	

	/**
	 * Return the user ID of the user who created this article.
	 * @return int
	 */
	function getUserId() {
		return $this->getProperty("IdUser");
	} // fn getUserId
	
	
	/**
	 * @param int value
	 */
	function setUserId($value) {
		return parent::setProperty("IdUser", $value);
	}
	
	
	/**
	 * Return true if the article is on the front page.
	 * @return boolean
	 */
	function onFrontPage() {
		return ($this->getProperty("OnFrontPage") == 'Y');
	} // fn onFrontPage
	
	
	/**
	 * @param boolean value
	 */
	function setOnFrontPage($p_value) {
		return parent::setProperty("OnFrontPage", $p_value?"Y":"N");
	} // fn setOnFrontPage
	
	
	/**
	 * @return boolean
	 */
	function onSection() {
		return ($this->getProperty("OnSection") == 'Y');
	} // fn onSection
	
	
	/**
	 * @param boolean value
	 */
	function setOnSection($p_value) {
		return parent::setProperty("OnSection", $p_value?"Y":"N");
	} // fn setOnSection
	
	
	/**
	 * @return string
	 * 		Can be "Y", "S", or "N".
	 */
	function getPublished() {
		return $this->getProperty("Published");
	} // fn isPublished
	
	
	/**
	 * Set the published state of the article.  
	 * Can be "Y" = "Yes", "S" = "Submitted", or "N" = "No".
	 * @param string value
	 */
	function setPublished($p_value) {
		return parent::setProperty("Published", $p_value);
	} // fn setIsPublished
	
	
	/**
	 * Return the date the article was created in the form YYYY-MM-DD.
	 * @return string
	 */
	function getUploadDate() {
		return $this->getProperty("UploadDate");
	} // fn getUploadDate
	
	
	/**
	 * @return string
	 */
	function getKeywords() {
		return $this->getProperty("Keywords");
	} // fn getKeywords
	
	
	/**
	 * @param string $value
	 */
	function setKeywords($p_value) {
		return parent::setProperty("Keywords", $p_value);
	} // fn setKeywords
	
	
	/**
	 * @return boolean
	 */
	function isPublic() {
		return ($this->getProperty("Public") == 'Y');
	} // fn isPublic
	
	
	/**
	 *
	 * @param boolean value
	 */
	function setIsPublic($p_value) {
		return parent::setProperty("Public", $p_value?"Y":"N");
	} // fn setIsPublic
	
	
	/**
	 * @return boolean
	 */
	function isIndexed() {
		return ($this->getProperty("IsIndexed") == 'Y');
	} // fn isIndexed
	
	
	/**
	 * @param boolean value
	 */
	function setIsIndexed($p_value) {
		return parent::setProperty("IsIndexed", $p_value?"Y":"N");
	} // fn setIsIndexed
	
	
	/**
	 * Return the user ID of the user who has locked the article.
	 * @return int
	 */
	function getLockedByUser() {
		return $this->getProperty("LockUser");
	} // fn getLockedByUser
	
	
	/**
	 * @param int value
	 */
	function setLockedByUser($p_value) {
		return parent::setProperty("LockUser", $p_value);
	} // fn setLockedByUser
	
	
	/**
	 * Get the time the article was locked.
	 *
	 * @return string
	 *		In the form of YYYY-MM-DD HH:MM:SS
	 */
	function getLockTime() {
		return $this->getProperty("LockTime");
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
		return $this->getProperty("ShortName");
	} // fn getShortName
	
	
	/**
	 * @param string value
	 */
	function setShortName($p_value) {
		return parent::setProperty("ShortName", $p_value);
	} // fn setShortName
	
	
	/**
	 * Return the ArticleType object for this article.
	 *
	 * @return ArticleType
	 */
	function getArticleTypeObject() {
		return new ArticleType($this->getProperty("Type"), 
			$this->getProperty("Number"), 
			$this->getProperty("IdLanguage"));
	} // fn getArticleTypeObject
	
	
	/**
	 * Return the articles written by the given user, within the given range.
	 * @return array
	 */
	function GetArticlesByUser($p_userId, $p_lowerLimit = 0, $p_upperLimit = 20) {
		global $Campsite;
		$queryStr = "SELECT * FROM Articles "
					." WHERE IdUser=$p_userId"
					." ORDER BY Number DESC, IdLanguage "
					." LIMIT $p_lowerLimit, $p_upperLimit";
		$query = $Campsite["db"]->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article($row["IdPublication"], $row["NrIssue"],
				$row["NrSection"], $row["IdLanguage"]);
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		return $articles;
	} // fn GetArticlesByUser
	
	
	function GetSubmittedArticles($p_lowerLimit = 0, $p_upperLimit = 20) {
		global $Campsite;
		$queryStr = "SELECT * FROM Articles"
	    			." WHERE Published = 'S' "
	    			." ORDER BY Number DESC, IdLanguage "
	    			." LIMIT $p_lowerLimit, $p_upperLimit";
		$query = $Campsite["db"]->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article($row["IdPublication"], $row["NrIssue"],
				$row["NrSection"], $row["IdLanguage"]);
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		return $articles;
	} // fn GetSubmittedArticles
	
} // class Article

?>