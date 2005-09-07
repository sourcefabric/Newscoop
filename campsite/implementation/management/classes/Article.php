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
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/ArticleType.php');
require_once($g_documentRoot.'/classes/ArticleImage.php');
require_once($g_documentRoot.'/classes/ArticleTopic.php');
require_once($g_documentRoot.'/classes/ArticleIndex.php');
require_once($g_documentRoot.'/classes/Language.php');

/**
 * @package Campsite
 */
class Article extends DatabaseObject {
	/**
	 * The column names used for the primary key.
	 * @var array
	 */
	var $m_keyColumnNames = array('IdPublication',
						   		  'NrIssue',
							   	  'NrSection',
							   	  'Number',
							   	  'IdLanguage');

	var $m_dbTableName = 'Articles';
	
	var $m_columnNames = array(
		// int - Publication ID 
		'IdPublication', 
		
		// int -Issue ID
		'NrIssue', 
		
		// int - Section ID
		'NrSection', 

		// int - Article ID
		'Number', 

		// int - Language ID,
		'IdLanguage',

		// string - Article Type
		'Type',
	
		// int - User ID of user who created the article
		'IdUser',
	
		// string - The title of the article.
		'Name',
	
		// string
		// Whether the article is on the front page or not.
	  	// This is represented as 'N' or 'Y'.
		'OnFrontPage',
	
		/**
		 * Whether or not the article is on the section or not.
		 * This is represented as 'N' or 'Y'.
		 * @var string
		 */
		'OnSection',
		'Published',
		'PublishDate',
		'UploadDate',
		'Keywords',
		'Public',
		'IsIndexed',
		'LockUser',
		'LockTime',
		'ShortName',
		'ArticleOrder');
		
	var $m_languageName = null;
	
	/**
	 * Construct by passing in the primary key to access the article in 
	 * the database.
	 *
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_sectionId
	 * @param int $p_languageId
	 * @param int $p_articleId
	 *		Not required when creating an article.
	 */
	function Article($p_publicationId = null, $p_issueId = null, $p_sectionId = null, 
					 $p_languageId = null, $p_articleId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['NrIssue'] = $p_issueId;
		$this->m_data['NrSection'] = $p_sectionId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_articleId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * A way for internal functions to call the superclass create function.
	 * @param array $p_values
	 */
	function __create($p_values = null) { return parent::create($p_values); }
	
	/**
	 * Create an article in the database.  Use the SET functions to
	 * change individual values.
	 *
	 * @param string $p_articleType
	 * @param string $p_name
	 * @return void
	 */
	function create($p_articleType, $p_name = null) 
	{
		global $Campsite;
		
		$this->m_data['Number'] = $this->__generateArticleId();
		$this->m_data['ArticleOrder'] = $this->m_data['Number'];
	
		// Create the record
		$values = array();
		if (!is_null($p_name)) {
			$values['Name'] = $p_name;
		}
		$values['ShortName'] = $this->m_data['Number'];
		$values['Type'] = $p_articleType;
		$values['Public'] = 'Y';
		
		// compute article order number
		$queryStr = 'SELECT MIN(ArticleOrder) AS min FROM Articles WHERE IdPublication = '
		          . $this->m_data['IdPublication'] . ' AND NrIssue = ' . $this->m_data['NrIssue']
		          . ' and NrSection = ' . $this->m_data['NrSection'];
		$articleOrder = $Campsite['db']->GetOne($queryStr) - 1;
		if ($articleOrder < 0)
			$articleOrder = $this->m_data['Number'];
		if ($articleOrder == 0) {
			$queryStr = 'UPDATE Articles SET ArticleOrder = ArticleOrder + 1 WHERE IdPublication = '
			          . $this->m_data['IdPublication'] . ' AND NrIssue = ' . $this->m_data['NrIssue']
			          . ' AND NrSection = ' . $this->m_data['NrSection'];
			$Campsite['db']->Execute($queryStr);
			$articleOrder = 1;
		}
		$values['ArticleOrder'] = $articleOrder;
		
		$success = parent::create($values);
		if (!$success) {
			return;
		}
		$this->setProperty('UploadDate', 'NOW()', true, true);
		$this->fetch();
	
		// Insert an entry into the article type table.
		$articleData =& new ArticleType($this->m_data['Type'], 
			$this->m_data['Number'], 
			$this->m_data['IdLanguage']);
		$articleData->create();
	} // fn create

	
	/**
	 * Create a unique identifier for an article.
	 * @access private
	 */
	function __generateArticleId() 
	{
	    global $Campsite;
		$queryStr = 'UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)';
		$Campsite['db']->Execute($queryStr);
		if ($Campsite['db']->Affected_Rows() <= 0) {
			// If we were not able to get an ID.
			return 0;
		}
		return $Campsite['db']->Insert_ID();	    
	} // fn __generateArticleId
	
	
	/**
	 * Create a copy of this article.
	 *
	 * @param int $p_destPublicationId -
	 *		The destination publication ID.
	 * @param int $p_destIssueId -
	 *		The destination issue ID.
	 * @param int $p_destSectionId -
	 * 		The destination section ID.
	 * @param int $p_userId -
	 *		The user creating the copy.  If null, keep the same user ID as the original.
	 * @param boolean $p_copyAllTranslations -
	 *     If true, all translations will be copied as well.
	 * @return mixed
	 *     If $p_copyAllTranslations is TRUE, return an array of copied articles.
	 *     If $p_copyAllTranslations is FALSE, return the copied Article.
	 */
	function copy($p_destPublicationId, $p_destIssueId, $p_destSectionId, 
	              $p_userId = null, $p_copyAllTranslations = false) 
	{
		global $Campsite;
		$copyArticles = array();
		if ($p_copyAllTranslations) {
		    // Get all translations for this article
		    $copyArticles =& Article::GetArticles($this->m_data['IdPublication'], 
		                                          $this->m_data['NrIssue'], 
		                                          $this->m_data['NrSection'], 
		                                          null, 
		                                          $this->m_data['Number']);
		}
		else {
		    $copyArticles[] = $this;
		}
		$newArticleId = $this->__generateArticleId();
		
		foreach ($copyArticles as $copyMe) {
    		// Construct the duplicate article object.
    		$articleCopy =& new Article();
    		$articleCopy->m_data['IdPublication'] = $p_destPublicationId; 
    		$articleCopy->m_data['NrIssue'] = $p_destIssueId; 
    		$articleCopy->m_data['NrSection'] = $p_destSectionId; 
    		$articleCopy->m_data['IdLanguage'] = $copyMe->m_data['IdLanguage']; 
    		$articleCopy->m_data['Number'] = $newArticleId; 
    		$values = array();
    		// Copy some attributes
    		$values['ShortName'] = $newArticleId;
    		$values['Type'] = $copyMe->m_data['Type'];
    		$values['OnFrontPage'] = $copyMe->m_data['OnFrontPage'];
    		$values['OnSection'] = $copyMe->m_data['OnFrontPage'];
    		$values['Public'] = $copyMe->m_data['Public'];
    		$values['ArticleOrder'] = $copyMe->m_data['ArticleOrder'];
    		$values['Keywords'] = $copyMe->m_data['Keywords'];
    		// Change some attributes
    		$values['Published'] = 'N';
    		$values['IsIndexed'] = 'N';		
    		$values['LockUser'] = 0;
    		$values['LockTime'] = 0;
    		if (!is_null($p_userId)) {
                $values['IdUser'] = $p_userId;
    		}
    		else {
    		    $values['IdUser'] = $copyMe->m_data['IdUser'];
    		}
    		$values['Name'] = $articleCopy->__getUniqueName($copyMe->m_data['Name']);

    		$articleCopy->__create($values);
    		$articleCopy->setProperty('UploadDate', 'NOW()', true, true);
    		    		
    		// Insert an entry into the article type table.
    		$newArticleData =& new ArticleType($articleCopy->m_data['Type'], 
    			$articleCopy->m_data['Number'], 
    			$articleCopy->m_data['IdLanguage']);
    		$newArticleData->create();
    		$origArticleData =& $copyMe->getArticleTypeObject();
    		$origArticleData->copyToExistingRecord($articleCopy->m_data['Number']);
    		
    		// Copy image pointers
    		ArticleImage::OnArticleCopy($copyMe->m_data['Number'], $articleCopy->m_data['Number']);
    
    		// Copy topic pointers
    		ArticleTopic::OnArticleCopy($copyMe->m_data['Number'], $articleCopy->m_data['Number']);
		}
		if ($p_copyAllTranslations) {
		    return $copyArticles;
		}
		else {
		  return array_pop($copyArticles);
		}
	} // fn copy
	
	
	function __getUniqueName($p_currentName) 
	{
	    global $Campsite;
		$origNewName = $p_currentName . " (duplicate";
		$newName = $origNewName .")";
		$count = 1;
		while (true) {
			$queryStr = 'SELECT * FROM Articles '
						.' WHERE IdPublication = '.$this->m_data['IdPublication']
						.' AND NrIssue = ' . $this->m_data['NrIssue']
						.' AND NrSection = ' . $this->m_data['NrSection']
						.' AND IdLanguage = ' . $this->m_data['IdLanguage']
						." AND Name = '" . mysql_escape_string($newName) . "'";
			$row = $Campsite['db']->GetRow($queryStr);
			if (count($row) > 0) {
				$newName = $origNewName.' '.++$count.')';
			}
			else {
				break;
			}
		}
	    return $newName;
	} // fn __getUniqueName
		
	
	/**
	 * Create a copy of the article, but make it a translation
	 * of the current one.
	 *
	 * @param int $p_languageId
	 * @param int $p_userId
	 * @param string $p_name
	 * @return Article
	 */
	function createTranslation($p_languageId, $p_userId, $p_name) 
	{
		global $Campsite;
		// Construct the duplicate article object.
		$articleCopy =& new Article();
		$articleCopy->m_data['IdPublication'] = $this->m_data['IdPublication']; 
		$articleCopy->m_data['NrIssue'] = $this->m_data['NrIssue']; 
		$articleCopy->m_data['NrSection'] = $this->m_data['NrSection']; 
		$articleCopy->m_data['IdLanguage'] = $p_languageId; 
		$articleCopy->m_data['Number'] = $this->m_data['Number']; 
		$values = array();
		// Copy some attributes
		$values['ShortName'] = $this->m_data['ShortName'];
		$values['Name'] = $p_name;
		$values['Type'] = $this->m_data['Type'];
		$values['OnFrontPage'] = $this->m_data['OnFrontPage'];
		$values['OnSection'] = $this->m_data['OnFrontPage'];
		$values['Public'] = $this->m_data['Public'];
		$values['ArticleOrder'] = $this->m_data['ArticleOrder'];
		// Change some attributes
		$values['Published'] = 'N';
		$values['IsIndexed'] = 'N';		
		$values['LockUser'] = 0;
		$values['LockTime'] = 0;
		$values['IdUser'] = $p_userId;

		// Create the record
		$success = $articleCopy->__create($values);
		if (!$success) {
			return;
		}

		$articleCopy->setProperty('UploadDate', 'NOW()', true, true);

		// Insert an entry into the article type table.
		$articleCopyData =& new ArticleType($articleCopy->m_data['Type'], 
			$articleCopy->m_data['Number'], $articleCopy->m_data['IdLanguage']);
		$articleCopyData->create();
		
		$origArticleData =& $this->getArticleTypeObject();
		$origArticleData->copyToExistingRecord($articleCopy->getArticleId(), $p_languageId);
		
		return $articleCopy;
	} // fn createTranslation
	
	
	/**
	 * Delete article from database.  This will 
	 * only delete one specific translation of the article.
	 */
	function delete() 
	{
		// Delete row from article type table.
		$articleData =& new ArticleType($this->m_data['Type'], 
			$this->m_data['Number'], 
			$this->m_data['IdLanguage']);
		$articleData->delete();
		
		// is this the last translation?
		if (count($this->getLanguages()) <= 1) {
			// Delete image pointers
			ArticleImage::OnArticleDelete($this->m_data['Number']);
			
			// Delete topics pointers
			ArticleTopic::OnArticleDelete($this->m_data['Number']);
			
			// Delete indexes
			ArticleIndex::OnArticleDelete($this->getPublicationId(), $this->getIssueId(),
				$this->getSectionId(), $this->getLanguageId(), $this->getArticleId());
		}
					
		// Delete row from Articles table.
		parent::delete();
	} // fn delete
	
	
	/**
	 * Get the time the article was locked.
	 *
	 * @return string
	 *		In the form of YYYY-MM-DD HH:MM:SS
	 */
	function getLockTime() 
	{
		return $this->getProperty('LockTime');
	} // fn getLockTime

	
	/**
	 * Return TRUE if the article is locked, FALSE if it isnt.
	 * @return boolean
	 */
	function isLocked() 
	{
	    if ( ($this->m_data['LockUser'] == 0) && ($this->m_data['LockTime'] == 0) ) {
	        return false;
	    } else {
	        return true;
	    }
	} // fn isLocked
	
	
	/**
	 * Lock the article with the given User ID.
	 *
	 * @param int $p_userId
	 *
	 */
	function lock($p_userId) 
	{
		$this->setProperty('LockUser', $p_userId);
		$this->setProperty('LockTime', 'NOW()', true, true);
	} // fn lock

	
	/**
	 * Unlock the article so anyone can edit it.
	 * @return void
	 */
	function unlock() 
	{
		$this->setProperty('LockUser', '0', false);
		$this->setProperty('LockTime', '0', false);
		$this->commit();
	} // fn unlock
	
	
	/**
	 * Return an array of Language objects, one for each
	 * type of language the article is written in.
	 *
	 * @return array
	 */
	function getLanguages() 
	{
		global $Campsite;
		$tmpLanguage  =& new Language();
		$columnNames = $tmpLanguage->getColumnNames(true);
	 	$queryStr = 'SELECT '.implode(',', $columnNames).' FROM Articles, Languages '
	 				.' WHERE IdPublication='.$this->m_data['IdPublication']
	 				.' AND NrIssue='.$this->m_data['NrIssue']
	 				.' AND NrSection='.$this->m_data['NrSection']
	 				.' AND Number='.$this->m_data['Number']
	 				.' AND Articles.IdLanguage=Languages.Id';
	 	$languages =& DbObjectArray::Create('Language', $queryStr);
		return $languages;
	} // fn getLanguages
	
	
	/**
	 * Return an array of Article objects, one for each
	 * type of language the article is written in.
	 *
	 * @return array
	 */
	function getTranslations() 
	{
		global $Campsite;
	 	$queryStr = 'SELECT '. implode(',', $this->m_columnNames).' FROM Articles '
	 				.' WHERE IdPublication='.$this->m_data['IdPublication']
	 				.' AND NrIssue='.$this->m_data['NrIssue']
	 				.' AND NrSection='.$this->m_data['NrSection']
	 				.' AND Number='.$this->m_data['Number'];
	 	$articles =& DbObjectArray::Create('Article', $queryStr);
		return $articles;
	} // fn getTranslations
	
	
	/**
	 * A simple way to get the name of the language the article is 
	 * written in.  The value is cached in case there are multiple
	 * calls to this function.
	 *
	 * @return string
	 */
	function getLanguageName() 
	{
		if (is_null($this->m_languageName)) {
			$language =& new Language($this->m_data['IdLanguage']);
			$this->m_languageName = $language->getNativeName();
		}
		return $this->m_languageName;
	} // fn getLanguageName
	
	
	/**
	 * Get the section that this article is in.
	 * @return object
	 */
	function getSection() 
	{
		global $Campsite;
	    $queryStr = 'SELECT * FROM Sections '
	    			.' WHERE IdPublication='.$this->getPublicationId()
	    			.' AND NrIssue='.$this->getIssueId()
	    			.' AND IdLanguage='.$this->getLanguageId();
		$query = $Campsite['db']->Execute($queryStr);
		if ($query->RecordCount() <= 0) {
			$queryStr = 'SELECT * FROM Sections '
						.' WHERE IdPublication='.$this->getPublicationId()
						.' AND NrIssue='.$this->getIssueId()
						.' LIMIT 1';
			$query = $Campsite['db']->Execute($queryStr);	
		}
		$row = $query->FetchRow();
		$section =& new Section($this->getPublicationId(), $this->getIssueId(),
			$this->getLanguageId());
		$section->fetch($row);
	    return $section;
	} // fn getSection
	
	
	/**
	 * Change the article's position in the order sequence
	 * relative to its current position.
	 *
	 * @param string $p_direction -
	 * 		Can be "up" or "down".
	 *
	 * @param int $p_spacesToMove -
	 *		The number of spaces to move the article.
	 *
	 * @return boolean
	 */
	function moveRelative($p_direction, $p_spacesToMove = 1)
	{
		global $Campsite;
		
		// Get the article that is in the final position where this
		// article will be moved to.
		$compareOperator = ($p_direction == 'up') ? '<' : '>';
		$order = ($p_direction == 'up') ? 'desc' : 'asc';
		$queryStr = 'SELECT DISTINCT(Number), ArticleOrder FROM Articles '
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
					.' AND ArticleOrder '.$compareOperator.' '.$this->m_data['ArticleOrder']
					//.' AND IdLanguage='.$this->m_data['IdLanguage']
					.' ORDER BY ArticleOrder ' . $order
		     		.' LIMIT '.($p_spacesToMove-1).', 1';
		$destRow = $Campsite['db']->GetRow($queryStr);
		if (!$destRow) {
			return false;
		}
		// Shift all articles one space between the source and destination article.
		$operator = ($p_direction == 'up') ? '+' : '-';
		$minArticleOrder = min($destRow['ArticleOrder'], $this->m_data['ArticleOrder']);
		$maxArticleOrder = max($destRow['ArticleOrder'], $this->m_data['ArticleOrder']);
		$queryStr = 'UPDATE Articles SET ArticleOrder = ArticleOrder '.$operator.' 1 '
					.' WHERE IdPublication = '. $this->m_data['IdPublication']
					.' AND NrIssue = ' . $this->m_data['NrIssue']
					.' AND NrSection = ' . $this->m_data['NrSection']
		     		.' AND ArticleOrder >= '.$minArticleOrder
		     		.' AND ArticleOrder <= '.$maxArticleOrder;
		$Campsite['db']->Execute($queryStr);
		
		// Change position of this article to the destination position.
		$queryStr = 'UPDATE Articles SET ArticleOrder = ' . $destRow['ArticleOrder']
					.' WHERE IdPublication = '. $this->m_data['IdPublication']
					.' AND NrIssue = ' . $this->m_data['NrIssue']
					.' AND NrSection = ' . $this->m_data['NrSection']
		     		.' AND Number = ' . $this->m_data['Number'];
		$Campsite['db']->Execute($queryStr);
		
		// Re-fetch this article to get the updated article order.
		$this->fetch();
		return true;
	} // fn moveRelative
	
	
	/**
	 * Move the article to the given position.
	 * @param int $p_position
	 * @return boolean
	 */
	function moveAbsolute($p_moveToPosition = 1) 
	{
		global $Campsite;
		// Get the article that is in the location we are moving
		// this one to.
		$queryStr = 'SELECT DISTINCT(Number), ArticleOrder FROM Articles '
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
		     		.' ORDER BY ArticleOrder ASC LIMIT '.($p_moveToPosition - 1).', 1';
		$destRow = $Campsite['db']->GetRow($queryStr);
		if (!$destRow) {
			return false;
		}
		if ($destRow['ArticleOrder'] == $this->m_data['ArticleOrder']) {
			return true;
		}
		if ($destRow['ArticleOrder'] > $this->m_data['ArticleOrder']) {
			$operator = '-';
		} else {
			$operator = '+';
		}
		// Reorder all the other articles in this section
		$minArticleOrder = min($destRow['ArticleOrder'], $this->m_data['ArticleOrder']);
		$maxArticleOrder = max($destRow['ArticleOrder'], $this->m_data['ArticleOrder']);
		$queryStr = 'UPDATE Articles '
					.' SET ArticleOrder = ArticleOrder '.$operator.' 1 '
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
		     		.' AND ArticleOrder >= '.$minArticleOrder
		     		.' AND ArticleOrder <= '.$maxArticleOrder;
		$Campsite['db']->Execute($queryStr);
		
		// Reposition this article.
		$queryStr = 'UPDATE Articles '
					.' SET ArticleOrder='.$destRow['ArticleOrder']
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
		     		.' AND Number='.$this->m_data['Number'];
		$Campsite['db']->Execute($queryStr);
		
		$this->fetch();
		return true;
	} // fn moveAbsolute
	

	/**
	 * Return true if the given user has permission to modify this article.
	 * @return boolean
	 */
	function userCanModify($p_user) 
	{
		$userCreatedArticle = ($this->m_data['IdUser'] == $p_user->getId());
		$articleIsNew = ($this->m_data['Published'] == 'N');
		if ($p_user->hasPermission('ChangeArticle') || ($userCreatedArticle && $articleIsNew)) {
			return true;
		}
		else {
			return false;
		}
	} // fn userCanModify
	
	
	/**
	 * Get the name of the dynamic article type table.
	 *
	 * @return string
	 */
	function getArticleTypeTableName() 
	{
		return 'X'.$this->m_data['Type'];
	} // fn getArticleTypeTableName
	
	
	/**
	 * @return int
	 */
	function getPublicationId() 
	{
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getIssueId() 
	{
		return $this->getProperty('NrIssue');
	} // fn getIssueId
	
	
	/**
	 * @return int
	 */
	function getSectionId() 
	{
		return $this->getProperty('NrSection');
	} // fn getSectionId
	
	
	/**
	 * @return int
	 */
	function getLanguageId() 
	{
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	/**
	 * @return int
	 */ 
	function getArticleId() 
	{
		return $this->getProperty('Number');
	} // fn getArticleId
	
	
	/**
	 * @return string
	 */
	function getTitle() 
	{
		return $this->getProperty('Name');
	} // fn getTitle
	
	
	/**
	 * @return string
	 */
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * Set the title of the article.
	 *
	 * @param string $p_title
	 *
	 * @return void
	 */
	function setTitle($p_title) 
	{
		return parent::setProperty('Name', $p_title);
	} // fn setTitle

	
	/**
	 * Get the article type.
	 * @return string
	 */
	function getType() 
	{
		return $this->getProperty('Type');
	} // fn getType
	

	/**
	 * Return the user ID of the user who created this article.
	 * @return int
	 */
	function getUserId() 
	{
		return $this->getProperty('IdUser');
	} // fn getUserId
	
	
	/**
	 * @param int value
	 */
	function setUserId($value) 
	{
		return parent::setProperty('IdUser', $value);
	}
	
	
	/**
	 * @return int
	 */
	function getOrder() 
	{
		return $this->getProperty('ArticleOrder');
	} // fn getOrder
	
	
	/**
	 * Return true if the article is on the front page.
	 * @return boolean
	 */
	function onFrontPage() 
	{
		return ($this->getProperty('OnFrontPage') == 'Y');
	} // fn onFrontPage
	
	
	/**
	 * @param boolean $p_value
	 */
	function setOnFrontPage($p_value) 
	{
		return parent::setProperty('OnFrontPage', $p_value?'Y':'N');
	} // fn setOnFrontPage
	
	
	/**
	 * @return boolean
	 */
	function onSectionPage() 
	{
		return ($this->getProperty('OnSection') == 'Y');
	} // fn onSectionPage
	
	
	/**
	 * Set whether the article will appear on the section page.
	 * @param boolean p_value
	 * @return boolean
	 */
	function setOnSectionPage($p_value) 
	{
		return parent::setProperty('OnSection', $p_value?'Y':'N');
	} // fn setOnSectionPage
	
	
	/**
	 * Return the current status of the article:
	 *   'Y' = "Published"
	 *	 'S' = "Submitted"
	 *   'N' = "New"
	 * 
	 * @return string
	 * 		Can be 'Y', 'S', or 'N'.
	 */
	function getPublished() 
	{
		return $this->getProperty('Published');
	} // fn isPublished
	
	
	/**
	 * Set the published state of the article.  
	 * 	   'Y' = 'Published'
	 *     'S' = 'Submitted'
	 *     'N' = 'New'
	 *
	 * @param string $p_value
	 */
	function setPublished($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value != 'Y') && ($p_value != 'S') && ($p_value != 'N')) {
			return false;
		}
		// If the article is being unpublished
		if ( ($this->getPublished() == 'Y') && ($p_value != 'Y') ) {
			// Delete indexes
			ArticleIndex::OnArticleDelete($this->getPublicationId(), $this->getIssueId(),
				$this->getSectionId(), $this->getLanguageId(), $this->getArticleId());
		}
		// If the article is being published
		if ( ($this->getPublished() != 'Y') && ($p_value == 'Y') ) {
    		$this->setIsIndexed(false);
		    $this->setProperty('PublishDate', 'NOW()', true, true);
		}
		// Unlock the article if it changes status.
		if ( $this->getPublished() != $p_value ) {
			$this->unlock();
		}
		return parent::setProperty('Published', $p_value);
	} // fn setPublished
	
	
	/**
	 * Get the date the article was published.
	 * @return string
	 */
	function getPublishDate()
	{
	    return $this->getProperty('PublishDate');
	} // fn getPublishDate
	
	
	/**
	 * Return the date the article was created in the form YYYY-MM-DD.
	 * @return string
	 */
	function getUploadDate() 
	{
		return $this->getProperty('UploadDate');
	} // fn getUploadDate
	
	
	/**
	 * @return string
	 */
	function getKeywords() 
	{
		return $this->getProperty('Keywords');
	} // fn getKeywords
	
	
	/**
	 * @param string $value
	 */
	function setKeywords($p_value) 
	{
		return parent::setProperty('Keywords', $p_value);
	} // fn setKeywords
	
	
	/**
	 * @return boolean
	 */
	function isPublic() 
	{
		return ($this->getProperty('Public') == 'Y');
	} // fn isPublic
	
	
	/**
	 *
	 * @param boolean value
	 */
	function setIsPublic($p_value) 
	{
		return parent::setProperty('Public', $p_value?'Y':'N');
	} // fn setIsPublic
	
	
	/**
	 * @return boolean
	 */
	function isIndexed() 
	{
		return ($this->getProperty('IsIndexed') == 'Y');
	} // fn isIndexed
	
	
	/**
	 * @param boolean value
	 */
	function setIsIndexed($p_value) 
	{
		return parent::setProperty('IsIndexed', $p_value?'Y':'N');
	} // fn setIsIndexed
	
	
	/**
	 * Return the user ID of the user who has locked the article.
	 * @return int
	 */
	function getLockedByUser() 
	{
		return $this->getProperty('LockUser');
	} // fn getLockedByUser
	
	
	/**
	 * @param int $p_value
	 */
	function setLockedByUser($p_value) 
	{
		return parent::setProperty('LockUser', $p_value);
	} // fn setLockedByUser
	
	
	/**
	 * @return string
	 */
	function getShortName() 
	{
		return $this->getProperty('ShortName');
	} // fn getShortName
	
	
	/**
	 * @param string value
	 */
	function setShortName($p_value) 
	{
		return parent::setProperty('ShortName', $p_value);
	} // fn setShortName
	
	
	/**
	 * Return the ArticleType object for this article.
	 *
	 * @return ArticleType
	 */
	function getArticleTypeObject() 
	{
		return new ArticleType($this->getProperty('Type'), 
			$this->getProperty('Number'), 
			$this->getProperty('IdLanguage'));
	} // fn getArticleTypeObject
	
	
//	/**
//	 * Create and return an array representation of an article for use in a template.
//	 * @return array
//	 */
//	function getTemplateVar() {
//		$templateVar = array();
//		$templateVar['publication_id'] = $this->IdPublication;
//		$templateVar['issue_id'] = $this->NrIssue;
//		$templateVar['section_id'] = $this->NrSection;
//		$templateVar['article_id'] = $this->Number;
//		$templateVar['language_id'] = $this->IdLanguage;
//		$templateVar['article_type'] = $this->Type;
//		$templateVar['user_id'] = $this->IdUser;
//		$templateVar['title'] = $this->Name;
//		$templateVar['on_front_page'] = $this->OnFrontPage;
//		$templateVar['on_section'] = $this->OnSection;
//		$templateVar['published'] = $this->Published;
//		$templateVar['upload_date'] = $this->UploadDate;
//		$templateVar['keywords'] = $this->Keywords;
//		$templateVar['is_public'] = $this->Public;
//		$templateVar['is_indexed'] = $this->IsIndexed;
//		$templateVar['locked_by_user'] = $this->LockUser;
//		$templateVar['lock_time'] = $this->LockTime;
//		$templateVar['short_name'] = $this->ShortName;
//		return $templateVar;
//	} // fn getTemplateVar


	/***************************************************************************/
	/* Static Functions                                                        */
	/***************************************************************************/
	
	/**
	 * Return the number of unique (language-independant) articles according
	 * to the given parameters.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_sectionId
	 * @return int
	 */
	function GetNumUniqueArticles($p_publicationId = null, $p_issueId = null, 
								  $p_sectionId = null) 
	{
		global $Campsite;
		$queryStr = 'SELECT COUNT(DISTINCT(Number)) FROM Articles';
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";			
		}
		if (!is_null($p_issueId)) {
			$whereClause[] = "NrIssue=$p_issueId";
		}
		if (!is_null($p_sectionId)) {
			$whereClause[] = "NrSection=$p_sectionId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE ' . implode(' AND ', $whereClause);
		}
		$result = $Campsite['db']->GetOne($queryStr);
		return $result;
	} // fn GetNumUniqueArticles
	
	
	/**
	 * Return an array of (array(Articles), int) where
	 * the array of articles are those written by the given user, within the given range,
	 * and the int is the total number of articles written by the user.
	 *
	 * @param int $p_userId
	 * @param int $p_start
	 * @param int $p_upperLimit
	 *
	 * @return array
	 */
	function GetArticlesByUser($p_userId, $p_start = 0, $p_upperLimit = 20) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM Articles '
					." WHERE IdUser=$p_userId"
					.' ORDER BY Number DESC, IdLanguage '
					." LIMIT $p_start, $p_upperLimit";
		$query = $Campsite['db']->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article($row['IdPublication'], $row['NrIssue'],
				$row['NrSection'], $row['IdLanguage']);
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		$queryStr = 'SELECT COUNT(*) FROM Articles '
					." WHERE IdUser=$p_userId"
					.' ORDER BY Number DESC, IdLanguage ';
		$totalArticles = $Campsite['db']->GetOne($queryStr);
		
		return array($articles, $totalArticles);
	} // fn GetArticlesByUser
	

	/**
	 * Get a list of submitted articles.
	 * Return an array of two elements: (array(Articles), int).
	 * The first element is an array of submitted articles.
	 * The second element is the total number of submitted articles.
	 *
	 * @param int $p_start
	 * @param int $p_upperLimit
	 * @return array
	 */
	function GetSubmittedArticles($p_start = 0, $p_upperLimit = 20) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM Articles'
	    			." WHERE Published = 'S' "
	    			.' ORDER BY Number DESC, IdLanguage '
	    			." LIMIT $p_start, $p_upperLimit";
		$query = $Campsite['db']->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article($row['IdPublication'], $row['NrIssue'],
				$row['NrSection'], $row['IdLanguage']);
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		$queryStr = 'SELECT COUNT(*) FROM Articles'
	    			." WHERE Published = 'S' "
	    			.' ORDER BY Number DESC, IdLanguage ';
	    $totalArticles = $Campsite['db']->GetOne($queryStr);
	    
		return array($articles, $totalArticles);
	} // fn GetSubmittedArticles
	
	
	/**
	 * Get the list of all languages that articles have been written in.
	 * @return array
	 */
	function GetAllLanguages() 
	{
		global $Campsite;
		$tmpLanguage =& new Language();
		$languageColumns = $tmpLanguage->getColumnNames(true);
		$languageColumns = implode(",", $languageColumns);
	 	$queryStr = 'SELECT DISTINCT(IdLanguage), '.$languageColumns
	 				.' FROM Articles, Languages '
	 				.' WHERE Articles.IdLanguage = Languages.Id';
	 	$languages =& DbObjectArray::Create('Language', $queryStr);
		return $languages;		
	} // fn GetAllLanguages
	
	
	/**
	 * Get a list of articles.  You can be as specific or as general as you
	 * like with the parameters: e.g. specifying only p_publication will get
	 * you all the articles in a particular publication.  Specifying all 
	 * parameters will get you all the articles in a particular section with
	 * the given language.
	 *
	 * @param int $p_publicationId -
	 *		The publication ID.
	 *
	 * @param int $p_issueId -
	 *		The issue ID.
	 *
	 * @param int $p_sectionId -
	 *		The section ID. 
	 *
	 * @param int $p_languageId -
	 *		The language ID.
	 *
	 * @param int $p_articleId -
	 *		The article ID.
	 *
	 * @param int $p_preferredLanguage -
	 *		If specified, list the articles in this language before others.
	 *
	 * @param int $p_numRows -
	 *		Max number of rows to fetch.
	 *
	 * @param int $p_startAt -
	 *		Index into the result array to begin at.
	 *
	 * @param boolean $p_numRowsIsUniqueRows -
	 *		Whether the number of rows stated in p_rows should be interpreted as
	 *		the number of articles to return regardless of how many times an 
	 *		article has been translated.  E.g. an article translated three times
	 *		would be counted as one article if this is set to TRUE, and counted
	 *		as three articles if this is set to FALSE.
	 *		Default: false
	 *
	 * @return array
	 *     Return an array of Article objects with indexes in sequential order
	 *     starting from zero.
	 */
	function GetArticles($p_publicationId = null, $p_issueId = null, 
						 $p_sectionId = null, $p_languageId = null, 
						 $p_articleId = null, $p_preferredLanguage = null, 
						 $p_numRows = null, $p_startAt = '', 
						 $p_numRowsIsUniqueRows = false) 
    {
		global $Campsite;
		
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";			
		}
		if (!is_null($p_issueId)) {
			$whereClause[] = "NrIssue=$p_issueId";
		}
		if (!is_null($p_sectionId)) {
			$whereClause[] = "NrSection=$p_sectionId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}
		if (!is_null($p_articleId)) {
			$whereClause[] = "Number=$p_articleId";
		}

		if ($p_numRowsIsUniqueRows) {
			$queryStr1 = 'SELECT DISTINCT(Number) FROM Articles ';
			if (count($whereClause) > 0) {
				$queryStr1 .= ' WHERE '. implode(' AND ', $whereClause);
			}
			if ($p_startAt !== '') {
				$p_startAt .= ',';
			}
			$queryStr1 .= ' ORDER BY ArticleOrder ASC, Number DESC ';
			if (!is_null($p_numRows)) {
				$queryStr1 .= ' LIMIT '.$p_startAt.$p_numRows;
			}
			$uniqueArticleNumbers = $Campsite['db']->GetCol($queryStr1);
		}
		
		$queryStr2 = 'SELECT *';
		// This causes the preferred language to be listed first.
		if (!is_null($p_preferredLanguage)) {
			$queryStr2 .= ", abs($p_preferredLanguage - IdLanguage) as LanguageOrder ";
		}
		$queryStr2 .= ' FROM Articles';
		
		// If selecting unique rows, specify those rows in the 
		// WHERE clause.
		$uniqueRowsClause = '';
		if ($p_numRowsIsUniqueRows) {
			$tmpClause = array();
			foreach ($uniqueArticleNumbers as $uniqueNumber) {
				$tmpClause[] = "Number = $uniqueNumber";
			}
			if (count($tmpClause) > 0) {
				$uniqueRowsClause = '(' .implode(' OR ', $tmpClause).')';
			}
		}
		
		// Add the WHERE clause.
		if ((count($whereClause) > 0) || ($uniqueRowsClause != '')) {
			$queryStr2 .= ' WHERE ';
			if (count($whereClause) > 0) {
				$queryStr2 .= '(' . implode(' AND ', $whereClause) .')';
			}
			if ($uniqueRowsClause != '') {
				if (count($whereClause) > 0) {
					$queryStr2 .= ' AND ';
				}
				$queryStr2 .= $uniqueRowsClause;
			}
		}
		
		// ORDER BY clause
		$orderBy = ' ORDER BY ArticleOrder ASC, Number DESC ';
		if (!is_null($p_preferredLanguage)) {
			$orderBy .= ', LanguageOrder ASC, IdLanguage ASC';
		}
		$queryStr2 .= $orderBy;
		
		// If not using the unique rows option,
		// use the limit clause to set the number of rows returned.
		if (!$p_numRowsIsUniqueRows) {
			if ($p_startAt !== '') {
				$p_startAt .= ',';
			}
			if (!is_null($p_numRows)) {
				$queryStr2 .= ' LIMIT '.$p_startAt.$p_numRows;
			}
		}

		$articles =& DbObjectArray::Create('Article', $queryStr2);
		return $articles;
	} // fn GetArticles
	
	
	/**
	 *
	 * @return array
	 */
	function GetRecentArticles($p_max) 
	{
	    global $Campsite;
	    $queryStr = "SELECT * FROM Articles "
	               ." WHERE Published='Y'"
	               ." ORDER BY PublishDate DESC"
	               ." LIMIT $p_max";
	    $result =& DbObjectArray::Create('Article', $queryStr);
	    return $result;
	} // fn GetRecentArticles
	
	
	/**
	 * Unlock all articles by the given user.
	 * @param int $p_userId
	 * @return void
	 */
	function UnlockByUser($p_userId) 
	{
		global $Campsite;
		$queryStr = 'UPDATE Articles SET LockUser=0, LockTime=0'
					." WHERE LockUser=$p_userId";
		$Campsite['db']->Execute($queryStr);
	} // fn UnlockByUser
} // class Article

?>