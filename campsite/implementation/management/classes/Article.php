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
	var $m_primaryKeyColumnNames = array("IdPublication",
								   		 "NrIssue",
							   		 	 "NrSection",
							   		 	 "Number",
							   		 	 "IdLanguage");

	var $m_dbTableName = "Articles";
	
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
		parent::DatabaseObject();
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
		if (function_exists ("incModFile")) {
			incModFile ();
		}

		// Insert an entry into the article type table.
		$articleData =& new ArticleType($this->Type, $this->Number, $this->IdLanguage);
		$articleData->create();
	} // fn create

	
	/**
	 * Delete article from database.
	 */
	function delete() {
		// Delete row from article type table.
		$articleData =& new ArticleType($this->Type, $this->Number, $this->IdLanguage);
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
		return parent::setProperty("Name", $p_title);
	} // fn setTitle

	
	/**
	 * Get the article type.
	 * @return string
	 */
	function getType() {
		return $this->Type;
	} // fn getType
	

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
		return parent::setProperty("IdUser", $value);
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
		return parent::setProperty("OnFrontPage", $value?"Y":"N");
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
		return parent::setProperty("OnSection", $value?"Y":"N");
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
	 * Can be "Y" = "Yes", "S" = "Submitted", or "N" = "No".
	 * @param string value
	 */
	function setPublished($value) {
		return parent::setProperty("Published", $value);
	} // fn setIsPublished
	
	
	/**
	 * Return the date the article was created in the form YYYY-MM-DD.
	 * @return string
	 */
	function getUploadDate() {
		return $this->UploadDate;
	} // fn getUploadDate
	
	
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
		return parent::setProperty("Keywords", $value);
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
		return parent::setProperty("Public", $value?"Y":"N");
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
		return parent::setProperty("IsIndexed", $value?"Y":"N");
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
		return parent::setProperty("LockUser", $value);
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
		return parent::setProperty("ShortName", $value);
	} // fn setShortName
	
	
	/**
	 * Return the ArticleType object for this article.
	 *
	 * @return ArticleType
	 */
	function getArticleTypeObject() {
		return new ArticleType($this->Type, $this->Number, $this->IdLanguage);
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