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
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/ArticleData.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Language.php');

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
		'ArticleOrder',
		'comments_enabled',
		'comments_locked',
		'time_updated');

	var $m_languageName = null;

	private static $s_defaultOrder = array('byPublication'=>'asc',
                                           'byIssue'=>'desc',
                                           'bySection'=>'desc',
                                           'bySectionOrder'=>'asc');

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
	 * If you would like to "place" the article using the publication ID,
	 * issue number, and section number, you can only do so if all three
	 * of these parameters are present.  Otherwise, the article will remain
 	 * unplaced.
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
		global $g_ado_db;

		$this->m_data['Number'] = $this->__generateArticleNumber();
		$this->m_data['ArticleOrder'] = $this->m_data['Number'];

		// Create the record
		$values = array();
		if (!is_null($p_name)) {
			$values['Name'] = $p_name;
		}
		// Only categorize the article if all three arguments:
		// $p_publicationId, $p_issueNumber, and $p_sectionNumber
		// are present.
		if (is_numeric($p_publicationId)
		    && is_numeric($p_issueNumber)
		    && is_numeric($p_sectionNumber)
		    && ($p_publicationId > 0)
		    && ($p_issueNumber > 0)
		    && ($p_sectionNumber > 0) ) {
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
		$articleOrder = $g_ado_db->GetOne($queryStr) - 1;
		if ($articleOrder < 0) {
			$articleOrder = $this->m_data['Number'];
		}
		if ($articleOrder == 0) {
			$queryStr = "UPDATE Articles SET ArticleOrder = ArticleOrder + 1$where";
			$g_ado_db->Execute($queryStr);
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

		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
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
	    global $g_ado_db;
		$queryStr = 'UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)';
		$g_ado_db->Execute($queryStr);
		if ($g_ado_db->Affected_Rows() <= 0) {
			// If we were not able to get an ID.
			return 0;
		}
		return $g_ado_db->Insert_ID();
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
		// It is an optimization to put these here because in most cases
		// you dont need these files.
		global $g_documentRoot;
		require_once($g_documentRoot.'/classes/ArticleImage.php');
		require_once($g_documentRoot.'/classes/ArticleTopic.php');
		require_once($g_documentRoot.'/classes/ArticleAttachment.php');
        require_once($g_documentRoot.'/classes/ArticleAudioclip.php');

		$copyArticles = array();
		if ($p_copyTranslations) {
		    // Get all translations for this article
		    $copyArticles = $this->getTranslations();

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
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
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

            // Copy audioclip pointers
            ArticleAudioclip::OnArticleCopy($copyMe->m_data['Number'], $articleCopy->m_data['Number']);

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
	 * @return boolean
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
		$success = false;
		if (count($columns) > 0) {
			$success = $this->update($columns);
			if ($success) {
				$this->positionAbsolute(1);
			}
		}
		return $success;
	} // fn move


	/**
	 * Return a unique name based on this article's name.
	 * The name returned will have the form "original_article_name (duplicate #)"
	 * @return string
	 */
	function getUniqueName($p_currentName)
	{
	    global $g_ado_db;
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
			$row = $g_ado_db->GetRow($queryStr);
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
        $values['comments_enabled'] = $this->m_data['comments_enabled'];
        $values['comments_locked'] = $this->m_data['comments_locked'];
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

		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
			$this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
			$articleCopy->getTitle(), $articleCopy->getLanguageName());
		Log::Message($logtext, null, 31);

		return $articleCopy;
	} // fn createTranslation


	/**
	 * Delete article from database.  This will
	 * only delete one specific translation of the article.
	 *
	 * @return boolean
	 */
	function delete()
	{
		// It is an optimization to put these here because in most cases
		// you dont need these files.
		global $g_documentRoot;
		require_once($g_documentRoot.'/classes/ArticleImage.php');
		require_once($g_documentRoot.'/classes/ArticleTopic.php');
		require_once($g_documentRoot.'/classes/ArticleIndex.php');
		require_once($g_documentRoot.'/classes/ArticleAttachment.php');
        require_once($g_documentRoot.'/classes/ArticleAudioclip.php');
		require_once($g_documentRoot.'/classes/ArticleComment.php');
		require_once($g_documentRoot.'/classes/ArticlePublish.php');

		// Delete scheduled publishing
		ArticlePublish::OnArticleDelete($this->m_data['Number'], $this->m_data['IdLanguage']);

		// Delete Article Comments
		ArticleComment::OnArticleDelete($this->m_data['Number'], $this->m_data['IdLanguage']);

		// is this the last translation?
		if (count($this->getLanguages()) <= 1) {
			// Delete image pointers
			ArticleImage::OnArticleDelete($this->m_data['Number']);

			// Delete topics pointers
			ArticleTopic::OnArticleDelete($this->m_data['Number']);

			// Delete file pointers
			ArticleAttachment::OnArticleDelete($this->m_data['Number']);

            // Delete audioclip pointers
            ArticleAudioclip::OnArticleDelete($this->m_data['Number']);

			// Delete indexes
			ArticleIndex::OnArticleDelete($this->getPublicationId(), $this->getIssueNumber(),
				$this->getSectionNumber(), $this->getLanguageId(), $this->getArticleNumber());
		}

		// Delete row from Articles table.
		$deleted = parent::delete();

		// Delete row from article type table.
		$articleData =& new ArticleData($this->m_data['Type'],
			$this->m_data['Number'],
			$this->m_data['IdLanguage']);
		$articleData->delete();

		if ($deleted) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
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
		return $this->m_data['LockTime'];
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
	 * Lock or unlock the article.
	 *
	 * Locking the article requires the user ID parameter.
	 *
	 * @param boolean $p_lock
	 * @param int $p_userId
	 * @return void
	 */
	function setIsLocked($p_lock, $p_userId = null)
	{
	    // Check parameters
        if ($p_lock && !is_numeric($p_userId)) {
            return;
        }

        // Dont change the article timestamp when the
        // article is locked.
        $lastModified = $this->m_data['time_updated'];
	    if ($p_lock) {
    		$this->setProperty('LockUser', $p_userId);
    		$this->setProperty('LockTime', 'NOW()', true, true);
	    } else {
    		$this->setProperty('LockUser', '0', false);
    		$this->setProperty('LockTime', '0', false);
    		$this->commit();
	    }
	    $this->setProperty('time_updated', $lastModified);
	} // fn setIsLocked


	/**
	 * Return an array of Language objects, one for each
	 * type of language the article is written in.
	 *
	 * @return array
	 */
	function getLanguages()
	{
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
	 * @param int $p_articleNumber
	 * 		Optional.  Use this if you call this function statically.
	 *
	 * @return array
	 */
	function getTranslations($p_articleNumber = null)
	{
		if (!is_null($p_articleNumber)) {
			$articleNumber = $p_articleNumber;
		} elseif (isset($this)) {
			$articleNumber = $this->m_data['Number'];
		} else {
			return array();
		}
	 	$queryStr = 'SELECT * FROM Articles '
	 				." WHERE Number=$articleNumber";
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
		global $g_ado_db;
	    $queryStr = 'SELECT * FROM Sections '
	    			.' WHERE IdPublication='.$this->getPublicationId()
	    			.' AND NrIssue='.$this->getIssueNumber()
	    			.' AND IdLanguage='.$this->getLanguageId();
		$query = $g_ado_db->Execute($queryStr);
		if ($query->RecordCount() <= 0) {
			$queryStr = 'SELECT * FROM Sections '
						.' WHERE IdPublication='.$this->getPublicationId()
						.' AND NrIssue='.$this->getIssueNumber()
						.' LIMIT 1';
			$query = $g_ado_db->Execute($queryStr);
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
		global $g_ado_db;

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
		$destRow = $g_ado_db->GetRow($queryStr);
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
			$destRow = $g_ado_db->GetRow($queryStr);
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
		$g_ado_db->Execute($queryStr2);

		// Change position of this article to the destination position.
		$queryStr3 = 'UPDATE Articles SET ArticleOrder = ' . $destRow['ArticleOrder']
					.' WHERE IdPublication = '. $this->m_data['IdPublication']
					.' AND NrIssue = ' . $this->m_data['NrIssue']
					.' AND NrSection = ' . $this->m_data['NrSection']
		     		.' AND Number = ' . $this->m_data['Number'];
		$g_ado_db->Execute($queryStr3);

		// Re-fetch this article to get the updated article order.
		$this->fetch();
		return true;
	} // fn positionRelative


	/**
	 * Move the article to the given position (i.e. reorder the article).
	 * @param int $p_moveToPosition
	 * @return boolean
	 */
	function positionAbsolute($p_moveToPosition = 1)
	{
		global $g_ado_db;
		// Get the article that is in the location we are moving
		// this one to.
		$queryStr = 'SELECT Number, IdLanguage, ArticleOrder FROM Articles '
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
		     		.' ORDER BY ArticleOrder ASC LIMIT '.($p_moveToPosition - 1).', 1';
		$destRow = $g_ado_db->GetRow($queryStr);
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
		$g_ado_db->Execute($queryStr);

		// Reposition this article.
		$queryStr = 'UPDATE Articles '
					.' SET ArticleOrder='.$destRow['ArticleOrder']
					.' WHERE IdPublication='.$this->m_data['IdPublication']
					.' AND NrIssue='.$this->m_data['NrIssue']
					.' AND NrSection='.$this->m_data['NrSection']
		     		.' AND Number='.$this->m_data['Number'];
		$g_ado_db->Execute($queryStr);

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
	 * 3) The user created the article and the article is in the "New" state.
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
	 * Get the publication ID of the publication that contains this article.
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->m_data['IdPublication'];
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
		return $this->m_data['NrIssue'];
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
	 * Get the section number that contains this article.
	 *
	 * @return int
	 */
	function getSectionNumber()
	{
		return $this->m_data['NrSection'];
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
	 * Return the language the article was written in.
	 *
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->m_data['IdLanguage'];
	} // fn getLanguageId


	/**
	 * Return the article number.  The article number is
	 * not necessarily unique.  Articles that have been translated into
	 * multiple languages all have the same article number.
	 * Therefore to uniquely identify an article you need both
	 * the article number and the language ID.
	 *
	 * @return int
	 */
	function getArticleNumber()
	{
		return $this->m_data['Number'];
	} // fn getArticleNumber


	/**
	 * Get the title of the article.
	 *
	 * @return string
	 */
	function getTitle()
	{
		return $this->m_data['Name'];
	} // fn getTitle


	/**
	 * Alias for getTitle().
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
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
		return $this->m_data['Type'];
	} // fn getType


	/**
	 * Get the logged in language's translation of the article type.
	 * @return string
	 */
	function getTranslateType()
	{
		$type = $this->getType();
		$typeObj =& new ArticleType($type);
		return $typeObj->getDisplayName();
	}


	/**
	 * Return the user ID of the user who created this article.
	 * @return int
	 */
	function getCreatorId()
	{
		return $this->m_data['IdUser'];
	} // fn getCreatorId


	/**
	 * Set the user ID of the user who created this article.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setCreatorId($p_value)
	{
		return parent::setProperty('IdUser', $p_value);
	} // fn setCreatorId


	/**
	 * Return an integer representing the order of the article
	 * within the section.  Note that these numbers are not sequential
	 * and can only be compared with the other articles in the section.
	 *
	 * @return int
	 */
	function getOrder()
	{
		return $this->m_data['ArticleOrder'];
	} // fn getOrder


	/**
	 * Return true if the article will appear on the front page.
	 *
	 * @return boolean
	 */
	function onFrontPage()
	{
		return ($this->m_data['OnFrontPage'] == 'Y');
	} // fn onFrontPage


	/**
	 * Set whether the article should appear on the front page.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setOnFrontPage($p_value)
	{
		return parent::setProperty('OnFrontPage', $p_value?'Y':'N');
	} // fn setOnFrontPage


	/**
	 * Return TRUE if this article will appear on the section page.
	 *
	 * @return boolean
	 */
	function onSectionPage()
	{
		return ($this->m_data['OnSection'] == 'Y');
	} // fn onSectionPage


	/**
	 * Set whether the article will appear on the section page.
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setOnSectionPage($p_value)
	{
		return parent::setProperty('OnSection', $p_value?'Y':'N');
	} // fn setOnSectionPage


	/**
	 * Return the current workflow state of the article:
	 *   'Y' = "Published"
	 *	 'S' = "Submitted"
	 *   'N' = "New"
	 *
	 * @return string
	 * 		Can be 'Y', 'S', or 'N'.
	 */
	function getWorkflowStatus()
	{
		return $this->m_data['Published'];
	} // fn getWorkflowStatus


	/**
	 * Return a human-readable string for the status of the workflow.
	 * This can be called statically or as a member function.
	 * If called statically, you must pass in a parameter.
	 *
	 * @param string $p_value
	 * @return string
	 */
	function getWorkflowDisplayString($p_value = null)
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
	} // fn getWorkflowDisplayString


	/**
	 * Set the workflow state of the article.
	 * 	   'Y' = 'Published'
	 *     'S' = 'Submitted'
	 *     'N' = 'New'
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setWorkflowStatus($p_value)
	{
		global $g_documentRoot;
		require_once($g_documentRoot.'/classes/ArticleIndex.php');

		$p_value = strtoupper($p_value);
		if ( ($p_value != 'Y') && ($p_value != 'S') && ($p_value != 'N')) {
			return false;
		}

		// If the article is being unpublished
		if ( ($this->getWorkflowStatus() == 'Y') && ($p_value != 'Y') ) {
			// Delete indexes
			ArticleIndex::OnArticleDelete($this->getPublicationId(), $this->getIssueNumber(),
				$this->getSectionNumber(), $this->getLanguageId(), $this->getArticleNumber());
		}
		// If the article is being published
		if ( ($this->getWorkflowStatus() != 'Y') && ($p_value == 'Y') ) {
    		$this->setIsIndexed(false);
		    $this->setProperty('PublishDate', 'NOW()', true, true);
		}
		// Unlock the article if it changes status.
		if ( $this->getWorkflowStatus() != $p_value ) {
			$this->setIsLocked(false);
		}
		$changed = parent::setProperty('Published', $p_value);
		if (function_exists("camp_load_translation_strings")) {
		    camp_load_translation_strings("api");
		}
		$logtext = getGS('Article #$1: "$2" status changed from $3 to $4.',
			$this->m_data['Number'], $this->m_data['Name'],
			$this->getWorkflowDisplayString(), $this->getWorkflowDisplayString($p_value))
			." (".getGS("Publication")." ".$this->m_data['IdPublication'].", "
			." ".getGS("Issue")." ".$this->m_data['NrIssue'].", "
			." ".getGS("Section")." ".$this->m_data['NrSection'].")";
		Log::Message($logtext, null, 35);
		return $changed;
	} // fn setWorkflowStatus


	/**
	 * Get the date the article was published.
	 * @return string
	 */
	function getPublishDate()
	{
	    return $this->m_data['PublishDate'];
	} // fn getPublishDate


	/**
	 * Set the date the article was published, parameter must be in the
	 * form YYYY-MM-DD.
	 * @param string $p_value
	 * @return boolean
	 */
	function setPublishDate($p_value)
	{
		return $this->setProperty('PublishDate', $p_value);
	} // fn setPublishDate


	/**
	 * Return the date the article was created in the
	 * form YYYY-MM-DD HH:MM:SS.
	 *
	 * @return string
	 */
	function getCreationDate()
	{
		return $this->m_data['UploadDate'];
	} // fn getCreationDate


	/**
	 * Set the date the article was created, parameter must be in the
	 * form YYYY-MM-DD.
	 * @param string $p_value
	 * @return boolean
	 */
	function setCreationDate($p_value)
	{
		return $this->setProperty('UploadDate', $p_value);
	} // fn setCreationDate


	/**
	 * Return the date the article was last modified in the
	 * form YYYY-MM-DD HH:MM:SS.
	 *
	 * @return string
	 */
	function getLastModified()
	{
	    // Deal with the differences between MySQL 4
	    // and MySQL 5.
	    if (strpos($this->m_data['time_updated'], "-") === false) {
	        $t = $this->m_data['time_updated'];
	        $str = substr($t, 0, 4).'-'.substr($t, 4, 2)
	               .'-'.substr($t, 6, 2).' '.substr($t, 8, 2)
	               .':'.substr($t, 10, 2).':'.substr($t, 12);
	        return $str;
	    } else {
	        return $this->m_data['time_updated'];
	    }
	} // fn getLastModified


	/**
	 * @return string
	 */
	function getKeywords()
	{
		global $g_documentRoot;
		require_once($g_documentRoot.'/classes/SystemPref.php');
		$keywords = $this->m_data['Keywords'];
		$keywordSeparator = SystemPref::Get("KeywordSeparator");
		return str_replace(",", $keywordSeparator, $keywords);
	} // fn getKeywords


	/**
	 * @param string $p_value
	 * @return boolean
	 */
	function setKeywords($p_value)
	{
		global $g_documentRoot;
		require_once($g_documentRoot.'/classes/SystemPref.php');
		$keywordsSeparator = SystemPref::Get('KeywordSeparator');
		$p_value = str_replace($keywordsSeparator, ",", $p_value);
		return parent::setProperty('Keywords', $p_value);
	} // fn setKeywords


	/**
	 * Return TRUE if this article was published.
	 *
	 * @return boolean
	 */
	function isPublished()
	{
		return ($this->m_data['Published'] == 'Y');
	} // fn isPublic


	/**
	 * Return TRUE if this article is viewable by non-subscribers.
	 *
	 * @return boolean
	 */
	function isPublic()
	{
		return ($this->m_data['Public'] == 'Y');
	} // fn isPublic


	/**
	 * Set whether this article is viewable by non-subscribers.
	 *
	 * @param boolean $p_value
	 * @return boolean
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
		return ($this->m_data['IsIndexed'] == 'Y');
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
		return $this->m_data['LockUser'];
	} // fn getLockedByUser


	/**
	 * Set the user who currently has a lock on the article.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	function setLockedByUser($p_value)
	{
	    // Dont change the timestamp when an article
	    // is locked.
	    $timestamp = $this->m_data['time_updated'];
		$success = parent::setProperty('LockUser', $p_value);
		if ($success) {
		    parent::setProperty('time_updated', $timestamp);
		}
		return $success;
	} // fn setLockedByUser


	/**
	 * Get the URL name for this article.
	 *
	 * @return string
	 */
	function getUrlName()
	{
		return $this->m_data['ShortName'];
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
		return new ArticleData($this->m_data['Type'],
			$this->m_data['Number'],
			$this->m_data['IdLanguage']);
	} // fn getArticleData


	/**
	 * Return TRUE if comments have been activated.
	 *
	 * @return boolean
	 */
	function commentsEnabled()
	{
	    return $this->m_data['comments_enabled'];
	} // fn commentsEnabled


	/**
	 * Set whether comments are enabled for this article.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setCommentsEnabled($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_enabled', $p_value);
	} // fn setCommentsEnabled


	/**
	 * Return TRUE if comments are locked for this article.
	 * This means that comments cannot be added.
	 *
	 * @return boolean
	 */
	function commentsLocked()
	{
	    return $this->m_data['comments_locked'];
	} // fn commentsLocked


	/**
	 * Set whether comments are locked for this article.
	 * If TRUE, this means that comments cannot be added to
	 * the article.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setCommentsLocked($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_locked', $p_value);
	} // fn setCommentsLocked


	/*****************************************************************/
    /** Static Functions                                             */
    /*****************************************************************/

    /**
     * Return an Article object having the given number
     * in the given publication, issue, section, language.
     *
     * @param int $p_articleNr
     *      The article number
     * @param int $p_publicationId
     *      The publication identifier
     * @param int $p_issueNr
     *      The issue number
     * @param int $p_sectionNr
     *      The section number
     * @param int $p_languageId
     *      The language identifier
     *
     * @return object|null
     *      An article object on success, null on failure
     */
    function GetByNumber($p_articleNr, $p_publicationId, $p_issueNr,
                         $p_sectionNr, $p_languageId)
    {
        global $g_ado_db;

        $queryStr = 'SELECT * FROM Articles '
            .'WHERE IdPublication='.$p_publicationId
            .' AND NrIssue='.$p_issueNr
            .' AND NrSection='.$p_sectionNr
            .' AND IdLanguage='.$p_languageId
            .' AND Number='.$p_articleNr;
        $result = DbObjectArray::Create('Article', $queryStr);

        return (is_array($result) && sizeof($result)) ? $result[0] : null;
    } // fn GetByNumber


    /**
     * Return an array of article having the given name
     * in the given publication / issue / section / language.
     *
     * @param string $p_name
     * @param int $p_publicationId
     * @param int $p_issueId
     * @param int $p_sectionId
     * @param int $p_languageId
     *
     * @return array
     */
    function GetByName($p_name, $p_publicationId = null, $p_issueId = null,
    					$p_sectionId = null, $p_languageId = null)
    {
        global $g_ado_db;
        $queryStr = 'SELECT * FROM Articles';
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
		$whereClause[] = "Name='$p_name'";
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE ' . implode(' AND ', $whereClause);
		}
		$result = DbObjectArray::Create("Article", $queryStr);
		return $result;
    } // fn GetByName


    /**
	 * Return the number of unique (language-independant) articles
	 * according to the given parameters.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_sectionId
	 * @return int
	 */
	function GetNumUniqueArticles($p_publicationId = null, $p_issueId = null,
								  $p_sectionId = null)
	{
		global $g_ado_db;
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
		$result = $g_ado_db->GetOne($queryStr);
		return $result;
	} // fn GetNumUniqueArticles


	/**
	 * Return an array of (array(Articles), int) where
	 * the array of articles are those written by the given user,
	 * within the given range, and the int is the total number of
	 * articles written by the user.
	 *
	 * @param int $p_userId
	 * @param int $p_start
	 * @param int $p_upperLimit
	 *
	 * @return array
	 */
	function GetArticlesByUser($p_userId, $p_start = 0, $p_upperLimit = 20)
	{
		global $g_ado_db;
		$queryStr = 'SELECT * FROM Articles '
					." WHERE IdUser=$p_userId"
					.' ORDER BY Number DESC, IdLanguage '
					." LIMIT $p_start, $p_upperLimit";
		$query = $g_ado_db->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article();
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		$queryStr = 'SELECT COUNT(*) FROM Articles '
					." WHERE IdUser=$p_userId"
					.' ORDER BY Number DESC, IdLanguage ';
		$totalArticles = $g_ado_db->GetOne($queryStr);

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
		global $g_ado_db;
		$tmpArticle =& new Article();
		$columnNames = $tmpArticle->getColumnNames(true);
		$queryStr = 'SELECT '.implode(", ", $columnNames)
					.' FROM Articles '
	    			." WHERE Published = 'S' "
	    			.' ORDER BY Number DESC, IdLanguage '
	    			." LIMIT $p_start, $p_upperLimit";
		$query = $g_ado_db->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article();
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		$queryStr = 'SELECT COUNT(*) FROM Articles'
	    			." WHERE Published = 'S' "
	    			.' ORDER BY Number DESC, IdLanguage ';
	    $totalArticles = $g_ado_db->GetOne($queryStr);

		return array($articles, $totalArticles);
	} // fn GetSubmittedArticles


	/**
	 * Get the articles that have no publication/issue/section.
	 *
	 * @param int $p_start
	 * @param int $p_maxRows
	 * @return array
	 *     An array of two elements:
	 *     An array of articles and the total number of articles.
	 */
	function GetUnplacedArticles($p_start = 0, $p_maxRows = 20)
	{
		global $g_ado_db;
		$tmpArticle =& new Article();
		$columnNames = $tmpArticle->getColumnNames(true);
		$queryStr = 'SELECT '.implode(", ", $columnNames)
					.' FROM Articles '
	    			." WHERE IdPublication=0 AND NrIssue=0 AND NrSection=0 "
	    			.' ORDER BY Number DESC, IdLanguage '
	    			." LIMIT $p_start, $p_maxRows";
		$query = $g_ado_db->Execute($queryStr);
		$articles = array();
		while ($row = $query->FetchRow()) {
			$tmpArticle =& new Article();
			$tmpArticle->fetch($row);
			$articles[] = $tmpArticle;
		}
		$queryStr = 'SELECT COUNT(*) FROM Articles'
	    			." WHERE IdPublication=0 AND NrIssue=0 AND NrSection=0 ";
	    $totalArticles = $g_ado_db->GetOne($queryStr);

		return array($articles, $totalArticles);
	} // fn GetUnplacedArticles


	/**
	 * Get the list of all languages that articles have been written in.
	 * @return array
	 */
	function GetAllLanguages()
	{
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
	 * @param int $p_issueNumber -
	 *		The issue number.
	 *
	 * @param int $p_sectionNumber -
	 *		The section number.
	 *
	 * @param int $p_languageId -
	 *		The language ID.
	 *
	 * @param array $p_sqlOptions
	 *
	 * @param boolean $p_countOnly
	 *
	 * @return array
	 *     Return an array of Article objects with indexes in sequential order
	 *     starting from zero.
	 */
	function GetArticles($p_publicationId = null,
						 $p_issueNumber = null,
						 $p_sectionNumber = null,
						 $p_languageId = null,
						 $p_sqlOptions = null,
						 $p_countOnly = false)
    {
		global $g_ado_db;

		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_issueNumber)) {
			$whereClause[] = "NrIssue=$p_issueNumber";
		}
		if (!is_null($p_sectionNumber)) {
			$whereClause[] = "NrSection=$p_sectionNumber";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}

		$selectStr = "*";
		if ($p_countOnly) {
			$selectStr = "COUNT(*)";
		}
		$queryStr = "SELECT $selectStr FROM Articles";

		// Add the WHERE clause.
		if ((count($whereClause) > 0)) {
			$queryStr .= ' WHERE (' . implode(' AND ', $whereClause) .')';
		}

		if ($p_countOnly) {
			$count = $g_ado_db->GetOne($queryStr);
			return $count;
		} else {
			if (is_null($p_sqlOptions)) {
				$p_sqlOptions = array();
			}
			if (!isset($p_sqlOptions['ORDER BY'])) {
				$p_sqlOptions['ORDER BY'] = array("ArticleOrder" => "ASC",
												  "Number" => "DESC");
			}
			$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
			$articles = DbObjectArray::Create('Article', $queryStr);
			return $articles;
		}
	} // fn GetArticles


	/**
	 * Get a list of articles.  You can be as specific or as general as you
	 * like with the parameters: e.g. specifying only p_publication will get
	 * you all the articles in a particular publication.  Specifying all
	 * parameters will get you all the articles in a particular section with
	 * the given language.
	 *
	 * This function differs from GetArticles in that any LIMIT set
	 * in $p_sqlOptions will be interpreted as the number of articles to
	 * return regardless of how many times an article has been translated.
	 * E.g. an article translated three times would be counted as one
	 * article, but counted as three articles in GetArticles().
	 *
	 * @param int $p_publicationId -
	 *		The publication ID.
	 *
	 * @param int $p_issueNumber -
	 *		The issue number.
	 *
	 * @param int $p_sectionNumber -
	 *		The section number.
	 *
	 * @param int $p_languageId -
	 *		The language ID.
	 *
	 * @param int $p_preferredLanguage -
	 *		If specified, list the articles in this language before others.
	 *
	 * @param array $p_sqlOptions
	 *
	 * @param boolean $p_countOnly
	 * 		Whether to run just the number of articles that match the
	 * 		search criteria.
	 *
	 * @return array
	 *     Return an array of Article objects.
	 */
	function GetArticlesGrouped($p_publicationId = null,
							    $p_issueNumber = null,
						        $p_sectionNumber = null,
						        $p_languageId = null,
						        $p_preferredLanguage = null,
						        $p_sqlOptions = null,
						        $p_countOnly = false)
    {
		global $g_ado_db;

		// Constraints
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_issueNumber)) {
			$whereClause[] = "NrIssue=$p_issueNumber";
		}
		if (!is_null($p_sectionNumber)) {
			$whereClause[] = "NrSection=$p_sectionNumber";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}

		$selectStr = "DISTINCT(Number)";
		if ($p_countOnly) {
			$selectStr = "COUNT(DISTINCT(Number))";
		}
		// Get the list of unique article numbers
		$queryStr1 = "SELECT $selectStr FROM Articles ";
		if (count($whereClause) > 0) {
			$queryStr1 .= ' WHERE '. implode(' AND ', $whereClause);
		}

		if ($p_countOnly) {
			$count = $g_ado_db->GetOne($queryStr1);
			return $count;
		}

		if (is_null($p_sqlOptions)) {
			$p_sqlOptions = array();
		}
		if (!isset($p_sqlOptions['ORDER BY'])) {
			$p_sqlOptions['ORDER BY'] = array("ArticleOrder" => "ASC",
											  "Number"=> "DESC");
		}
		$queryStr1 = DatabaseObject::ProcessOptions($queryStr1, $p_sqlOptions);
		$uniqueArticleNumbers = $g_ado_db->GetCol($queryStr1);

		// Get the articles
		$queryStr2 = 'SELECT *';
		// This causes the preferred language to be listed first.
		if (!is_null($p_preferredLanguage)) {
			$queryStr2 .= ", abs($p_preferredLanguage - IdLanguage) as LanguageOrder ";
		}
		$queryStr2 .= ' FROM Articles';

		$uniqueRowsClause = '';
		if (count($uniqueArticleNumbers) > 0) {
			$uniqueRowsClause = '(Number=' .implode(' OR Number=', $uniqueArticleNumbers).')';
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
		if (!is_null($p_preferredLanguage)) {
			$p_sqlOptions['ORDER BY']['LanguageOrder'] = "ASC";
			$p_sqlOptions['ORDER BY']['IdLanguage'] = "ASC";
		}
		unset($p_sqlOptions['LIMIT']);
		$queryStr2 = DatabaseObject::ProcessOptions($queryStr2, $p_sqlOptions);
		$articles = DbObjectArray::Create('Article', $queryStr2);
		return $articles;
	} // fn GetUniqueArticles


	/**
	 * Return the number of articles of the given type.
	 * @param string $p_type
	 *		Article Type
	 * @return int
	 */
	function GetNumArticlesOfType($p_type)
	{
		global $g_ado_db;
		$queryStr ="SELECT COUNT(*) FROM Articles WHERE Type='$p_type'";
		return $g_ado_db->GetOne($queryStr);
	} // fn GetNumArticlesOfType


	/**
	 * Return an array of article objects of a certain type.
	 *
	 * @param string p_type
	 *
	 * @return array
	 */
	function GetArticlesOfType($p_type)
	{
		global $g_ado_db;
		$sql = "SELECT * FROM Articles WHERE Type='$p_type'";
		$articles = DbObjectArray::Create('Article', $sql);
		return $articles;
	} // fn GetArticlesOfType


	/**
	 * Get the $p_max number of the most recently published articles.
	 * @param int $p_max
	 * @return array
	 */
	function GetRecentArticles($p_max)
	{
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
		global $g_ado_db;
		$queryStr = 'UPDATE Articles SET LockUser=0, LockTime=0, time_updated=time_updated'
					." WHERE LockUser=$p_userId";
		$g_ado_db->Execute($queryStr);
	} // fn UnlockByUser


    /**
     *
     */
    public static function GetList($p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0)
    {
        global $g_ado_db;

        if (!is_array($p_parameters)) {
            return null;
        }

        $sqlClauseObj = new SQLSelectClause();

        // gets the column list to be retrieved for the database table
        $tmpArticle =& new Article();
		$columnNames = $tmpArticle->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $sqlClauseObj->addColumn($columnName);
        }

        // sets the name of the table for the this database object
        $sqlClauseObj->setTable($tmpArticle->getDbTableName());
        unset($tmpArticle);

        // parses the given parameters in order to build the WHERE part of
        // the SQL SELECT sentence
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param, $sqlClauseObj);

            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
            $sqlClauseObj->addWhere($whereCondition);
        }

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the ORDER BY condition
        foreach ($p_order as $orderColumn => $orderDirection) {
            $sqlClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        // sets the LIMIT start and offset values
        $sqlClauseObj->setLimit($p_start, $p_limit);

        // builds the SQL query
        $sqlQuery = $sqlClauseObj->buildQuery();

        // runs the SQL query
        $articles = $g_ado_db->Execute($sqlQuery);
        if (!$articles) {
            return null;
        }

        // builds the array of Article objects
        $articlesList = array();
        foreach ($articles as $article) {
            $articlesList[] = new Article($article['IdLanguage'],
                                          $article['Number']);
        }

        return $articlesList;
    } // fn GetList


    /**
     *
     */
    private static function ProcessListParameters($p_param, &$p_sqlClause)
    {
        $conditionOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'name':
            $conditionOperation['left'] = 'Name';
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;
        case 'number':
            $conditionOperation['left'] = 'Number';
            $conditionOperation['right'] = (int)$p_param->getRightOperand();
            break;
        case 'keyword':
            $conditionOperation['left'] = 'Keywords';
            $conditionOperation['symbol'] = 'LIKE';
            $conditionOperation['right'] = '%'.$p_param->getRightOperand().'%';
            break;
        case 'onfrontpage':
            $conditionOperation['left'] = 'OnFrontPage';
            $conditionOperation['right'] = (strtolower($p_param->getRightOperand()) == 'on') ? 'Y' : 'N';
            break;
        case 'onsection':
            $conditionOperation['left'] = 'OnSection';
            $conditionOperation['right'] = (strtolower($p_param->getRightOperand()) == 'on') ? 'Y' : 'N';
            break;
        case 'upload_date':
            $conditionOperation['left'] = 'UploadDate';
            $conditionOperation['right'] = (int)$p_param->getRightOperand();
            break;
        case 'public':
            $conditionOperation['left'] = 'Public';
            $conditionOperation['right'] = (strtolower($p_param->getRightOperand()) == 'on') ? 'Y' : 'N';
            break;
        case 'type':
            $conditionOperation['left'] = 'Type';
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;
        case 'matchalltopics':
            $p_sqlClause->addColumn('COUNT(*) AS matches');
            $join1 = ' LEFT JOIN ArticleTopics ON Articles.Number = ArticleTopics.NrArticle ';
            $p_sqlClause->addJoin($join1);
            $p_sqlClause->addJoin($join2);

            // matchAllTopics
            //
            // SELECT Articles.*, COUNT(*) AS matches
            //        FROM Articles
            //        LEFT JOIN ArticleTopics
            //            ON Articles.Number = ArticleTopics.NrArticle
            //        WHERE ArticleTopics.TopicId IN (5, 9, 11)
            //        GROUP BY ArticleTopics.NrArticle
            //        HAVING matches = 3;


            if (strtolower($p_param->getRightOperand()) == 'on') {

            } else {

            }

            $leftOperand = '';
            $rightOperand = '';
            break;
        case 'topic':
            $join1 = ' LEFT JOIN ArticleTopics ON Articles.Number = ArticleTopics.NrArticle ';
            $join2 = ' LEFT JOIN Topics ON ArticleTopics.TopicId = Topics.Id ';
            $p_sqlClause->addJoin($join1);
            $p_sqlClause->addJoin($join2);

            $conditionOperation['left'] = 'Topics.Name';
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;
        }

        if (!isset($conditionOperation['symbol'])) {
            $operatorObj = $p_param->getOperator();
            $conditionOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $conditionOperation;
    } // fn ProcessListParameters


    /**
     * Returns a list of Article objects selected based on the given keywords
     *
     * @param array $p_keywords
     * @param array $p_constraints
     * @param array $p_order
     * @return array
     */
    public static function SearchByKeyword(array $p_keywords,
                                           array $p_constraints = array(),
                                           array $p_order = array(),
                                           $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;

        $selectClauseObj = new SQLSelectClause();

        // set tables and joins between tables
        $selectClauseObj->setTable('KeywordIndex');
        $selectClauseObj->addJoin('LEFT JOIN ArticleIndex ON KeywordIndex.Id = ArticleIndex.IdKeyword');
        $selectClauseObj->addJoin('LEFT JOIN Articles ON ArticleIndex.NrArticle = Articles.Number'
                                                   . ' AND ArticleIndex.IdPublication = Articles.IdPublication'
                                                   . ' AND ArticleIndex.IdLanguage = Articles.IdLanguage'
                                                   . ' AND ArticleIndex.NrIssue = Articles.NrIssue'
                                                   . ' AND ArticleIndex.NrSection = Articles.NrSection');

        // set search keywords
        foreach ($p_keywords as $keyword) {
            $selectClauseObj->addWhere("KeywordIndex.Keyword = '" . $g_ado_db->escape($keyword) . "'");
        }

        // set other constraints
        foreach ($p_constraints as $constraint) {
            $selectClauseObj->addWhere($constraint->getLeftOperand()
                                       . $constraint->getOperator()->getSymbol('sql')
                                       . $constraint->getRightOperand());
        }

        // create the count clause object
        $countClauseObj = clone $selectClauseObj;

        // set the columns for the select clause
        $selectClauseObj->addColumn('Articles.Number');
        $selectClauseObj->addColumn('Articles.IdLanguage');

        // set the order for the select clause
        $p_order = count($p_order) > 0 ? $p_order : Article::$s_defaultOrder;
        $order = Article::ProcessListOrder($p_order);
        foreach ($order as $orderField=>$orderDirection) {
            $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
        }

        // sets the LIMIT start and offset values
        $selectClauseObj->setLimit($p_start, $p_limit);

        // set the column for the count clause
        $countClauseObj->addColumn('COUNT(*)');

        $selectQuery = $selectClauseObj->buildQuery();
        $articles = $g_ado_db->GetAll($selectQuery);
        foreach ($articles as $article) {
            $articlesList[] = new Article($article['IdLanguage'], $article['Number']);
        }
        $countQuery = $countClauseObj->buildQuery();
        $p_count = $g_ado_db->GetOne($countQuery);
        return $articlesList;
    }


    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $field=>$direction) {
            $dbField = null;
            switch (strtolower($field)) {
                case 'bynumber':
                    $dbField = 'Articles.Number';
                    break;
                case 'byname':
                    $dbField = 'Articles.Name';
                    break;
                case 'bydate':
                case 'bycreationdate':
                    $dbField = 'Articles.UploadDate';
                    break;
                case 'bypublishdate':
                    $dbField = 'Articles.PublicationDate';
                    break;
                case 'bypublication':
                    $dbField = 'Articles.IdPublication';
                    break;
                case 'byissue':
                    $dbField = 'Articles.NrIssue';
                    break;
                case 'bysection':
                    $dbField = 'Articles.NrSection';
                    break;
                case 'bylanguage':
                    $dbField = 'Articles.IdLanguage';
                    break;
                case 'bysectionorder':
                    $dbField = 'Articles.ArticleOrder';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }

} // class Article

?>