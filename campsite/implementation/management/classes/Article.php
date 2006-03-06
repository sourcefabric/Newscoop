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
require_once($g_documentRoot.'/classes/ArticleData.php');
require_once($g_documentRoot.'/classes/ArticleImage.php');
require_once($g_documentRoot.'/classes/ArticleTopic.php');
require_once($g_documentRoot.'/classes/ArticleIndex.php');
require_once($g_documentRoot.'/classes/ArticleAttachment.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/SystemPref.php');

/**
 * @package Campsite
 */
class Article extends DatabaseObject {
	/**
	 * The column names used for the primary key.
	 * @var array
	 */
	var $m_keyColumnNames = array('Number',
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
	 * @param int $p_languageId
	 * @param int $p_articleNumber
	 *		Not required when creating an article.
	 */
	function Article($p_languageId = null, $p_articleNumber = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_articleNumber;
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
	 * @param int $p_publicationId
	 * @param int $p_issueNumber
	 * @param int $p_sectionNumber
	 * @return void
	 */
	function create($p_articleType, $p_name = null, $p_publicationId = null, $p_issueNumber = null, $p_sectionNumber = null)
	{
		global $Campsite;

		$this->m_data['Number'] = $this->__generateArticleNumber();
		$this->m_data['ArticleOrder'] = $this->m_data['Number'];

		// Create the record
		$values = array();
		if (!is_null($p_name)) {
			$values['Name'] = $p_name;
		}
		if (is_numeric($p_publicationId) && is_numeric($p_issueNumber) && is_numeric($p_sectionNumber)) {
			$values['IdPublication'] = $p_publicationId;
			$values['NrIssue'] = $p_issueNumber;
			$values['NrSection'] = $p_sectionNumber;
		}
		$values['ShortName'] = $this->m_data['Number'];
		$values['Type'] = $p_articleType;
		$values['Public'] = 'Y';

		if (!is_null($p_publicationId) && $p_publicationId > 0) {
			$where = " WHERE IdPublication = $p_publicationId AND NrIssue = $p_issueNumber"
		    		. " and NrSection = $p_sectionNumber";
		} else {
			$where = '';
		}

		// compute article order number
		$queryStr = "SELECT MIN(ArticleOrder) AS min FROM Articles$where";
		$articleOrder = $Campsite['db']->GetOne($queryStr) - 1;
		if ($articleOrder < 0) {
			$articleOrder = $this->m_data['Number'];
		}
		if ($articleOrder == 0) {
			$queryStr = "UPDATE Articles SET ArticleOrder = ArticleOrder + 1$where";
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
		$articleData =& new ArticleData($this->m_data['Type'],
			$this->m_data['Number'],
			$this->m_data['IdLanguage']);
		$articleData->create();

		if (function_exists("camp_load_language")) { camp_load_language("api");	}
		$logtext = getGS('Article #$1 "$2" ($3) created.',
			$this->m_data['Number'], $this->m_data['Name'], $this->getLanguageName());
		Log::Message($logtext, null, 31);
	} // fn create


	/**
	 * Create a unique identifier for an article.
	 * @access private
	 */
	function __generateArticleNumber()
	{
	    global $Campsite;
		$queryStr = 'UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)';
		$Campsite['db']->Execute($queryStr);
		if ($Campsite['db']->Affected_Rows() <= 0) {
			// If we were not able to get an ID.
			return 0;
		}
		return $Campsite['db']->Insert_ID();
	} // fn __generateArticleNumber


	/**
	 * Create a copy of this article.
	 *
	 * @param int $p_destPublicationId -
	 *		The destination publication ID.
	 * @param int $p_destIssueNumber -
	 *		The destination issue number.
	 * @param int $p_destSectionNumber -
	 * 		The destination section number.
	 * @param int $p_userId -
	 *		The user creating the copy.  If null, keep the same user ID as the original.
	 * @param mixed $p_copyTranslations -
	 *		If false (default), only this article will be copied.
	 * 		If true, all translations will be copied.
	 *		If an array is passed, the translations given will be copied.
	 *		Any translations that do not exist will be ignored.
	 *
	 * @return Article
	 *     If $p_copyTranslations is TRUE or an array, return an array of newly created articles.
	 *     If $p_copyTranslations is FALSE, return the new Article.
	 */
	function copy($p_destPublicationId = 0, $p_destIssueNumber = 0, $p_destSectionNumber = 0,
	              $p_userId = null, $p_copyTranslations = false)
	{
		global $Campsite;
		$copyArticles = array();
		if ($p_copyTranslations) {
		    // Get all translations for this article
		    $copyArticles = Article::GetArticles($this->m_data['IdPublication'],
		                                          $this->m_data['NrIssue'],
		                                          $this->m_data['NrSection'],
		                                          null,
		                                          $this->m_data['Number']);
		    // Remove any translations that are not requested to be translated.
		    if (is_array($p_copyTranslations)) {
		    	$tmpArray = array();
		    	foreach ($copyArticles as $tmpArticle) {
		    		if (in_array($tmpArticle->m_data['IdLanguage'], $p_copyTranslations)) {
		    			$tmpArray[] = $tmpArticle;
		    		}
		    	}
		    	$copyArticles = $tmpArray;
		    }
		} else {
		    $copyArticles[] = $this;
		}
		$newArticleNumber = $this->__generateArticleNumber();

		// Load translation file for log message.
		if (function_exists("camp_load_language")) { camp_load_language("api");	}
		$logtext = '';
		$newArticles = array();
		foreach ($copyArticles as $copyMe) {
    		// Construct the duplicate article object.
    		$articleCopy =& new Article();
    		$articleCopy->m_data['IdPublication'] = $p_destPublicationId;
    		$articleCopy->m_data['NrIssue'] = $p_destIssueNumber;
    		$articleCopy->m_data['NrSection'] = $p_destSectionNumber;
    		$articleCopy->m_data['IdLanguage'] = $copyMe->m_data['IdLanguage'];
    		$articleCopy->m_data['Number'] = $newArticleNumber;
    		$values = array();
    		// Copy some attributes
    		$values['ShortName'] = $newArticleNumber;
    		$values['Type'] = $copyMe->m_data['Type'];
    		$values['OnFrontPage'] = $copyMe->m_data['OnFrontPage'];
    		$values['OnSection'] = $copyMe->m_data['OnSection'];
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
    		} else {
    		    $values['IdUser'] = $copyMe->m_data['IdUser'];
    		}
    		$values['Name'] = $articleCopy->getUniqueName($copyMe->m_data['Name']);

    		$articleCopy->__create($values);
    		$articleCopy->setProperty('UploadDate', 'NOW()', true, true);

    		// Insert an entry into the article type table.
    		$newArticleData =& new ArticleData($articleCopy->m_data['Type'],
    			$articleCopy->m_data['Number'],
    			$articleCopy->m_data['IdLanguage']);
    		$newArticleData->create();
    		$origArticleData = $copyMe->getArticleData();
    		$origArticleData->copyToExistingRecord($articleCopy->m_data['Number']);

    		// Copy image pointers
    		ArticleImage::OnArticleCopy($copyMe->m_data['Number'], $articleCopy->m_data['Number']);

    		// Copy topic pointers
    		ArticleTopic::OnArticleCopy($copyMe->m_data['Number'], $articleCopy->m_data['Number']);

    		// Copy file pointers
    		ArticleAttachment::OnArticleCopy($copyMe->m_data['Number'], $articleCopy->m_data['Number']);

    		// Position the new article at the beginning of the section
    		$articleCopy->positionAbsolute(1);

    		$newArticles[] = $articleCopy;
			$logtext .= getGS('Article #$1 "$2" ($3) copied to Article #$3. ',
				$copyMe->getArticleNumber(), $copyMe->getName(),
				$copyMe->getLanguageId(), $articleCopy->getArticleNumber());
		}

		Log::Message($logtext, null, 155);
		if ($p_copyTranslations) {
		    return $newArticles;
		} else {
		  return array_pop($newArticles);
		}
	} // fn copy


	/**
	 * This is a convenience function to move an article from
	 * one section to another.
	 *
	 * @param int $p_destPublicationId -
	 *		The destination publication ID.
	 * @param int $p_destIssueNumber -
	 *		The destination issue number.
	 * @param int $p_destSectionNumber -
	 * 		The destination section number.
	 *
	 * @return void
	 */
	function move($p_destPublicationId = 0, $p_destIssueNumber = 0, $p_destSectionNumber = 0)
	{
		$columns = array();
		if ($this->m_data["IdPublication"] != $p_destPublicationId) {
			$columns["IdPublication"] = $p_destPublicationId;
		}
		if ($this->m_data["NrIssue"] != $p_destIssueNumber) {
			$columns["NrIssue"] = $p_destIssueNumber;
		}
		if ($this->m_data["NrSection"] != $p_destSectionNumber) {
			$columns["NrSection"] = $p_destSectionNumber;
		}
		if (count($columns) > 0) {
			$this->update($columns);
		}
	} // fn move


	/**
	 * Return a unique name based on this article's name.
	 * The name returned will have the form "original_article_name (duplicate #)"
	 * @return string
	 */
	function getUniqueName($p_currentName)
	{
	    global $Campsite;
		$origNewName = $p_currentName . " (".getGS("Duplicate");
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
			} else {
				break;
			}
		}
	    return $newName;
	} // fn getUniqueName


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
		$values['Type'] = $this->m_data['Type'];
		$values['OnFrontPage'] = $this->m_data['OnFrontPage'];
		$values['OnSection'] = $this->m_data['OnFrontPage'];
		$values['Public'] = $this->m_data['Public'];
		$values['ArticleOrder'] = $this->m_data['ArticleOrder'];
		// Change some attributes
		$values['Name'] = $p_name;
		$values['Published'] = 'N';
		$values['IsIndexed'] = 'N';
		$values['LockUser'] = 0;
		$values['LockTime'] = 0;
		$values['IdUser'] = $p_userId;

		// Create the record
		$success = $articleCopy->__create($values);
		if (!$success) {
			return false;
		}

		$articleCopy->setProperty('UploadDate', 'NOW()', true, true);

		// Insert an entry into the article type table.
		$articleCopyData =& new ArticleData($articleCopy->m_data['Type'],
			$articleCopy->m_data['Number'], $articleCopy->m_data['IdLanguage']);
		$articleCopyData->create();

		$origArticleData = $this->getArticleData();
		$origArticleData->copyToExistingRecord($articleCopy->getArticleNumber(), $p_languageId);

		if (function_exists("camp_load_language")) { camp_load_language("api");	}
		$logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
			$this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
			$articleCopy->getTitle(), $articleCopy->getLanguageName());
		Log::Message($logtext, null, 31);

		return $articleCopy;
	} // fn createTranslation


	/**
	 * Delete article from database.  This will
	 * only delete one specific translation of the article.
	 */
	function delete()
	{
		// Delete row from article type table.
		$articleData =& new ArticleData($this->m_data['Type'],
			$this->m_data['Number'],
			$this->m_data['IdLanguage']);
		$articleData->delete();

		// is this the last translation?
		if (count($this->getLanguages()) <= 1) {
			// Delete image pointers
			ArticleImage::OnArticleDelete($this->m_data['Number']);

			// Delete topics pointers
			ArticleTopic::OnArticleDelete($this->m_data['Number']);

			// Delete file pointers
			ArticleAttachment::OnArticleDelete($this->m_data['Number']);

			// Delete indexes
			ArticleIndex::OnArticleDelete($this->getPublicationId(), $this->getIssueNumber(),
				$this->getSectionNumber(), $this->getLanguageId(), $this->getArticleNumber());
		}

		// Delete row from Articles table.
		$deleted = parent::delete();

		if ($deleted) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Article #$1: "$2" ($3) deleted.',
				$this->m_data['Number'], $this->m_data['Name'],	$this->getLanguageName())
				." (".getGS("Publication")." ".$this->m_data['IdPublication'].", "
				." ".getGS("Issue")." ".$this->m_data['NrIssue'].", "
				." ".getGS("Section")." ".$this->m_data['NrSection'].")";
			Log::Message($logtext, null, 32);
		}
		return $deleted;
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
	 	$languages = DbObjectArray::Create('Language', $queryStr);
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
	 	$articles = DbObjectArray::Create('Article', $queryStr);
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
	    			.' AND NrIssue='.$this->getIssueNumber()
	    			.' AND IdLanguage='.$this->getLanguageId();
		$query = $Campsite['db']->Execute($queryStr);
		if ($query->RecordCount() <= 0) {
			$queryStr = 'SELECT * FROM Sections '
						.' WHERE IdPublication='.$this->getPublicationId()
						.' AND NrIssue='.$this->getIssueNumber()
						.' LIMIT 1';
			$query = $Campsite['db']->Execute($queryStr);
		}
		$row = $query->FetchRow();
		$section =& new Section($this->getPublicationId(), $this->getIssueNumber(),
			$this->getLanguageId());
		$section->fetch($row);
	    return $section;
	} // fn getSection


	/**
	 * Change the article's position in the order sequence
	 * relative to its current position.
	 *
	 * @param string $p_direction -
	 * 		Can be "up" or "down".  "Up" means towards the beginning of the list,
	 * 		and "down" means towards the end of the list.
	 *
	 * @param int $p_spacesToMove -
	 *		The number of spaces to move the article.
	 *
	 * @return boolean
	 */
	function positionRelative($p_direction, $p_spacesToMove = 1)
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
					.' ORDER BY ArticleOrder ' . $order
		     		.' LIMIT '.($p_spacesToMove-1).', 1';
		$destRow = $Campsite['db']->GetRow($queryStr);
		if (!$destRow) {
			// Special case: there was a bug when you duplicated articles that
			// caused them to have the same order number.  So we check here if
			// there are any articles that match the order number of the current
			// article.  The end result will be that this article will have
			// a different order number than all the articles it used to share it
			// with.  However, the other articles will still have the same
			// order number, which means that the article may appear to 'jump'
			// across multiple articles.
			$queryStr = 'SELECT DISTINCT(Number), ArticleOrder FROM Articles '
						.' WHERE IdPublication='.$this->m_data['IdPublication']
						.' AND NrIssue='.$this->m_data['NrIssue']
						.' AND NrSection='.$this->m_data['NrSection']
						.' AND ArticleOrder='.$this->m_data['ArticleOrder']
			     		.' LIMIT '.($p_spacesToMove-1).', 1';
			$destRow = $Campsite['db']->GetRow($queryStr);
			if (!$destRow) {
				return false;
			}
		}
		// Shift all articles one space between the source and destination article.
		$operator = ($p_direction == 'up') ? '+' : '-';
		$minArticleOrder = min($destRow['ArticleOrder'], $this->m_data['ArticleOrder']);
		$maxArticleOrder = max($destRow['ArticleOrder'], $this->m_data['ArticleOrder']);
		$queryStr2 = 'UPDATE Articles SET ArticleOrder = ArticleOrder '.$operator.' 1 '
					.' WHERE IdPublication = '. $this->m_data['IdPublication']
					.' AND NrIssue = ' . $this->m_data['NrIssue']
					.' AND NrSection = ' . $this->m_data['NrSection']
		     		.' AND ArticleOrder >= '.$minArticleOrder
		     		.' AND ArticleOrder <= '.$maxArticleOrder;
		$Campsite['db']->Execute($queryStr2);

		// Change position of this article to the destination position.
		$queryStr3 = 'UPDATE Articles SET ArticleOrder = ' . $destRow['ArticleOrder']
					.' WHERE IdPublication = '. $this->m_data['IdPublication']
					.' AND NrIssue = ' . $this->m_data['NrIssue']
					.' AND NrSection = ' . $this->m_data['NrSection']
		     		.' AND Number = ' . $this->m_data['Number'];
		$Campsite['db']->Execute($queryStr3);

		// Re-fetch this article to get the updated article order.
		$this->fetch();
		return true;
	} // fn positionRelative


	/**
	 * Move the article to the given position.
	 * @param int $p_position
	 * @return boolean
	 */
	function positionAbsolute($p_moveToPosition = 1)
	{
		global $Campsite;
		// Get the article that is in the location we are moving
		// this one to.
		$queryStr = 'SELECT Number, IdLanguage, ArticleOrder FROM Articles '
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
		     		.' ORDER BY ArticleOrder ASC LIMIT '.($p_moveToPosition - 1).', 1';
		$destRow = $Campsite['db']->GetRow($queryStr);
		if (!$destRow) {
			return false;
		}
		if ($destRow['ArticleOrder'] == $this->m_data['ArticleOrder']) {
			// Move the destination down one.
			$destArticle =& new Article($destRow['IdLanguage'], $destRow['Number']);
			$destArticle->positionRelative("down", 1);
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
	} // fn positionAbsolute


	/**
	 * Return true if the given user has permission to modify the content of this article.
	 *
	 * 1) Publishers can always edit.
	 * 2) Users who have the ChangeArticle right can edit as long as the
	 *    article is not published.  i.e. they can edit ALL articles that are
	 *    new or submitted.
	 * 3) Users with the AddArticle right can edit as long as they created
	 *    the article, and the article is in the "New" state.
	 *
	 * @return boolean
	 */
	function userCanModify($p_user)
	{
		$userCreatedArticle = ($this->m_data['IdUser'] == $p_user->getUserId());
		$articleIsNew = ($this->m_data['Published'] == 'N');
		$articleIsNotPublished = (($this->m_data['Published'] == 'N') || ($this->m_data['Published'] == 'S'));
		if ($p_user->hasPermission('Publish')
			|| ($p_user->hasPermission('ChangeArticle') && $articleIsNotPublished)
			|| ($userCreatedArticle && $articleIsNew)) {
			return true;
		} else {
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
	 * Set the publication ID.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setPublicationId($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('IdPublication', $p_value);
		} else {
			return false;
		}
	} // fn setPublicationId


	/**
	 * Get the issue that the article resides within.
	 *
	 * @return int
	 */
	function getIssueNumber()
	{
		return $this->getProperty('NrIssue');
	} // fn getIssueNumber


	/**
	 * Set the issue number.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setIssueNumber($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('NrIssue', $p_value);
		} else {
			return false;
		}
	} // fn setIssueNumber


	/**
	 * @return int
	 */
	function getSectionNumber()
	{
		return $this->getProperty('NrSection');
	} // fn getSectionNumber


	/**
	 * Set the section number.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setSectionNumber($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('NrSection', $p_value);
		} else {
			return false;
		}
	} // fn setSectionNumber


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
	function getArticleNumber()
	{
		return $this->getProperty('Number');
	} // fn getArticleNumber


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
	function getCreatorId()
	{
		return $this->getProperty('IdUser');
	} // fn getCreatorId


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


	function getPublishedDisplayString($p_value = null)
	{
		if (is_null($p_value)) {
			$p_value = $this->m_data['Published'];
		}
		if ( ($p_value != 'Y') && ($p_value != 'S') && ($p_value != 'N')) {
			return '';
		}
		switch ($p_value) {
		case 'Y':
			return getGS("Published");
		case 'S':
			return getGS("Submitted");
		case 'N':
			return getGS("New");
		}
	} // fn getPublishedDisplayString


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
			ArticleIndex::OnArticleDelete($this->getPublicationId(), $this->getIssueNumber(),
				$this->getSectionNumber(), $this->getLanguageId(), $this->getArticleNumber());
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
		$changed = parent::setProperty('Published', $p_value);
		if (function_exists("camp_load_language")) { camp_load_language("api");	}
		$logtext = getGS('Article #$1: "$2" status changed from $3 to $4.',
			$this->m_data['Number'], $this->m_data['Name'],
			$this->getPublishedDisplayString(), $this->getPublishedDisplayString($p_value))
			." (".getGS("Publication")." ".$this->m_data['IdPublication'].", "
			." ".getGS("Issue")." ".$this->m_data['NrIssue'].", "
			." ".getGS("Section")." ".$this->m_data['NrSection'].")";
		Log::Message($logtext, null, 35);
		return $changed;
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
	function getCreationDate()
	{
		return $this->getProperty('UploadDate');
	} // fn getCreationDate


	/**
	 * Set the date the article was created, parameter must be in the form YYYY-MM-DD.
	 * @param string $p_value
	 * @return boolean
	 */
	function setCreationDate($p_value)
	{
		return $this->setProperty('UploadDate', $p_value);
	} // fn setCreationDate


	/**
	 * @return string
	 */
	function getKeywords()
	{
		$keywords = $this->getProperty('Keywords');
		$keywordSeparator = SystemPref::Get("KeywordSeparator");
		return str_replace(",", $keywordSeparator, $keywords);
	} // fn getKeywords


	/**
	 * @param string $value
	 */
	function setKeywords($p_value)
	{
		$keywordsSeparator = SystemPref::Get('KeywordSeparator');
		$p_value = str_replace($keywordsSeparator, ",", $p_value);
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
	function getUrlName()
	{
		return $this->getProperty('ShortName');
	} // fn getUrlName


	/**
	 * @param string value
	 */
	function setUrlName($p_value)
	{
		return parent::setProperty('ShortName', $p_value);
	} // fn setUrlName


	/**
	 * Return the ArticleData object for this article.
	 *
	 * @return ArticleData
	 */
	function getArticleData()
	{
		return new ArticleData($this->getProperty('Type'),
			$this->getProperty('Number'),
			$this->getProperty('IdLanguage'));
	} // fn getArticleData


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
			$tmpArticle =& new Article();
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
		$tmpArticle =& new Article();
		$columnNames = $tmpArticle->getColumnNames(true);
		$queryStr = 'SELECT '.implode(", ", $columnNames)
					.' FROM Articles '
	    			." WHERE Published = 'S' "
	    			.' ORDER BY Number DESC, IdLanguage '
	    			." LIMIT $p_start, $p_upperLimit";
		$query = $Campsite['db']->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article();
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
	 * Get the articles that have no publication/issue/section.
	 *
	 * @param int $p_start
	 * @param int $p_maxRows
	 */
	function GetUnplacedArticles($p_start = 0, $p_maxRows = 20)
	{
		global $Campsite;
		$tmpArticle =& new Article();
		$columnNames = $tmpArticle->getColumnNames(true);
		$queryStr = 'SELECT '.implode(", ", $columnNames)
					.' FROM Articles '
	    			." WHERE IdPublication=0 AND NrIssue=0 AND NrSection=0 "
	    			.' ORDER BY Number DESC, IdLanguage '
	    			." LIMIT $p_start, $p_maxRows";
		$query = $Campsite['db']->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article();
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		$queryStr = 'SELECT COUNT(*) FROM Articles'
	    			." WHERE IdPublication=0 AND NrIssue=0 AND NrSection=0 ";
	    $totalArticles = $Campsite['db']->GetOne($queryStr);

		return array($articles, $totalArticles);
	} // fn GetUnplacedArticles


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
	 	$languages = DbObjectArray::Create('Language', $queryStr);
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
	 * @param int $p_articleNumber -
	 *		The article number.
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
						 $p_articleNumber = null, $p_preferredLanguage = null,
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
		if (!is_null($p_articleNumber)) {
			$whereClause[] = "Number=$p_articleNumber";
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

		$articles = DbObjectArray::Create('Article', $queryStr2);
		return $articles;
	} // fn GetArticles


	/**
	 * Return the number of articles of the given type.
	 * @param string $p_type
	 *		Article Type
	 * @return int
	 */
	function GetNumArticlesOfType($p_type)
	{
		global $Campsite;
		$queryStr ="SELECT COUNT(*) FROM Articles WHERE Type='$p_type'";
		return $Campsite['db']->GetOne($queryStr);
	} // fn GetNumArticlesOfType


	/**
	 * Get the $p_max number of the most recently published articles.
	 * @param int $p_max
	 * @return array
	 */
	function GetRecentArticles($p_max)
	{
	    global $Campsite;
	    $queryStr = "SELECT * FROM Articles "
	               ." WHERE Published='Y'"
	               ." ORDER BY PublishDate DESC"
	               ." LIMIT $p_max";
	    $result = DbObjectArray::Create('Article', $queryStr);
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