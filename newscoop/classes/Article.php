<?php
/**
 * @package Newscoop
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleData.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMap.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');
require_once dirname(__FILE__) . '/GeoMap.php';

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

        // int - User ID of user who manages the article in Campsite
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
        'time_updated',
        'object_id');

    var $m_languageName = null;

    var $m_cacheUpdate = false;

    var $m_published;

    private static $s_defaultOrder = array(array('field'=>'byPublication', 'dir'=>'ASC'),
                                           array('field'=>'byIssue', 'dir'=>'DESC'),
                                           array('field'=>'bySection', 'dir'=>'ASC'),
                                           array('field'=>'bySectionOrder', 'dir'=>'ASC'));

    private static $s_regularParameters = array('idpublication'=>'Articles.IdPublication',
    											'publication'=>'Articles.IdPublication',
                                                'nrissue'=>'Articles.NrIssue',
                                                'issue'=>'Articles.NrIssue',
                                                'nrsection'=>'Articles.NrSection',
                                                'section'=>'Articles.NrSection',
                                                'idlanguage'=>'Articles.IdLanguage',
                                                'name'=>'Articles.Name',
                                                'number'=>'Articles.Number',
                                                'upload_date'=>'DATE(Articles.UploadDate)',
                                                'publish_date'=>'DATE(Articles.PublishDate)',
                                                'type'=>'Articles.Type',
                                                'keyword'=>'Articles.Keywords',
                                                'onfrontpage'=>'Articles.OnFrontPage',
                                                'onsection'=>'Articles.OnSection',
                                                'public'=>'Articles.Public',
                                                'published'=>'Articles.Published',
                                                'workflow_status'=>'Articles.Published',
                                                'issue_published'=>'Issues.Published',
                                                'reads'=>'RequestObjects.request_count',
                                                'iduser' => 'Articles.IdUser',
                                            );

    /**
     * Construct by passing in the primary key to access the article in
     * the database.
     *
     * @param int $p_languageId
     * @param int $p_articleNumber
     *		Not required when creating an article.
     */
    public function Article($p_languageId = null, $p_articleNumber = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['IdLanguage'] = $p_languageId;
        $this->m_data['Number'] = $p_articleNumber;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
        $this->m_published = $this->isPublished();
    } // constructor

    /**
     * On article update destructor calls the template cache update process.
     */
    public function __destruct()
    {
        if ($this->m_cacheUpdate && ($this->isPublished() || $this->m_published)
        && CampTemplateCache::factory()) {
            CampTemplateCache::factory()->update(array(
                'language' => $this->getLanguageId(),
                'publication' => $this->getPublicationId(),
                'issue' => $this->getIssueNumber(),
                'section' => $this->getSectionNumber(),
                'article' => $this->getArticleNumber(),
            ));
        }
    }

    /**
     * Set the given column name to the given value.
     * The object's internal variable will also be updated.
     * If the value hasnt changed, the database will not be updated.
     * Note: You cannot set $p_commit to FALSE and $p_isSql to TRUE
     * at the same time.
     *
     * @param string $p_dbColumnName
     *      The name of the column that is to be updated.
     *
     * @param string $p_value
     *      The value to set.
     *
     * @param boolean $p_commit
     *      If set to true, the value will be written to the database immediately.
     *      If set to false, the value will not be written to the database.
     *      Default is true.
     *
     * @param boolean $p_isSql
     *      Set this to TRUE if p_value consists of SQL commands.
     *      There is no way to know what the result of the command is,
     *      so we will need to refetch the value from the database in
     *      order to update the internal variable's value.
     *
     * @return boolean
     *      TRUE on success, FALSE on error.
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        $status = parent::setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql);
        $this->m_cacheUpdate = true;
        return $status;
    }

    /**
     * Fetch a single record from the database for the given key.
     *
     * @param array $p_recordSet
     *      If the record has already been fetched and we just need to
     *      assign the data to the object's internal member variable.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function fetch($p_recordSet = null)
    {
        $res = parent::fetch($p_recordSet);
        if ($this->exists()) {
            settype($this->m_data['IdPublication'], 'integer');
            settype($this->m_data['NrIssue'], 'integer');
            settype($this->m_data['NrSection'], 'integer');
            settype($this->m_data['IdLanguage'], 'integer');
            settype($this->m_data['Number'], 'integer');
            settype($this->m_data['IdUser'], 'integer');
            settype($this->m_data['LockUser'], 'integer');
            settype($this->m_data['ArticleOrder'], 'integer');
        }
        return $res;
    }

    /**
     * Check if an article with the same name exists in the translation destination
     *
     * @param string $p_translation_title
     *      the desired title for the translated article
     * @param int $p_translation_language
     * 		the id of the translation language
     *
     * @return boolean
     *      TRUE if an article with the same name exists, FALSE otherwise
     */
    public function translationTitleExists($p_translation_title, $p_translation_language)
    {
    	global $g_ado_db;

		$idPublication =  $this->m_data['IdPublication'];
		$nrIssue = $this->m_data['NrIssue'];
		$nrSection = $this->m_data['NrSection'];


        $where = " WHERE IdPublication = $idPublication AND NrIssue = $nrIssue"
                    . " AND NrSection = $nrSection"
                    . " AND IdLanguage = $p_translation_language"
                    . " AND Name = '$p_translation_title'";

        $queryStr = "SELECT Number FROM Articles$where";

        $articleNumber = $g_ado_db->GetOne($queryStr);

        if ( $articleNumber > 0 ) {
        	return TRUE;
        } else {
        	return FALSE;
        }
    }


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    public function __create($p_values = null) { return parent::create($p_values); }


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
    public function create($p_articleType, $p_name = null, $p_publicationId = null, $p_issueNumber = null, $p_sectionNumber = null)
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
            $values['IdPublication'] = (int)$p_publicationId;
            $values['NrIssue'] = (int)$p_issueNumber;
            $values['NrSection'] = (int)$p_sectionNumber;
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
        $articleOrder = $g_ado_db->GetOne($queryStr);
        if (is_null($articleOrder) || !isset($values['NrSection'])) {
            $articleOrder = $this->m_data['Number'];
        } else {
            $increment = $articleOrder > 0 ? 1 : 2;
            $queryStr = "UPDATE Articles SET ArticleOrder = ArticleOrder + $increment $where";
            $g_ado_db->Execute($queryStr);
            $articleOrder = 1;
        }
        $values['ArticleOrder'] = $articleOrder;

        $success = parent::create($values);
        if (!$success) {
            return;
        }
        $this->fetch();
        $this->setProperty('UploadDate', 'NOW()', true, true);

        // Insert an entry into the article type table.
        $articleData = new ArticleData($this->m_data['Type'],
            $this->m_data['Number'],
            $this->m_data['IdLanguage']);
        $articleData->create();

        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        Log::ArticleMessage($this, getGS('Article created.'), null, 31, TRUE);
    } // fn create


    /**
     * Create a unique identifier for an article.
     * @access private
     */
    public function __generateArticleNumber()
    {
        global $g_ado_db;
        $queryStr = 'UPDATE AutoId SET ArticleId=LAST_INSERT_ID(ArticleId + 1)';
        $g_ado_db->Execute($queryStr);
        if ($g_ado_db->Affected_Rows() <= 0) {
            // If we were not able to get an ID.
            return 0;
        }
        return (int)$g_ado_db->Insert_ID();
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
    public function copy($p_destPublicationId = 0, $p_destIssueNumber = 0,
                         $p_destSectionNumber = 0, $p_userId = null,
                         $p_copyTranslations = false)
    {
        global $g_ado_db;

        // It is an optimization to put these here because in most cases
        // you dont need these files.
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');

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

        // geo-map copying
        if (0 < count($copyArticles))
        {
            $map_user_id = $p_userId;
            if (is_null($map_user_id)) {
                $map_user_id = $this->m_data['IdUser'];
            }

            $map_artilce_src = (int)$this->m_data['Number'];
            $map_artilce_dest = (int)$newArticleNumber;
            $map_translations = array();
            foreach ($copyArticles as $copyMe) {
                $map_translations[] = (int)$copyMe->m_data['IdLanguage'];
            }
            Geo_Map::OnArticleCopy($map_artilce_src, $map_artilce_dest, $map_translations, $map_user_id);
        }

        // Load translation file for log message.
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $articleOrder = null;
        $logtext = '';
        $newArticles = array();
        foreach ($copyArticles as $copyMe) {
            // Construct the duplicate article object.
            $articleCopy = new Article();
            $articleCopy->m_data['IdPublication'] = (int)$p_destPublicationId;
            $articleCopy->m_data['NrIssue'] = (int)$p_destIssueNumber;
            $articleCopy->m_data['NrSection'] = (int)$p_destSectionNumber;
            $articleCopy->m_data['IdLanguage'] = (int)$copyMe->m_data['IdLanguage'];
            $articleCopy->m_data['Number'] = (int)$newArticleNumber;
            $values = array();
            // Copy some attributes
            $values['ShortName'] = $newArticleNumber;
            $values['Type'] = $copyMe->m_data['Type'];
            $values['OnFrontPage'] = $copyMe->m_data['OnFrontPage'];
            $values['OnSection'] = $copyMe->m_data['OnSection'];
            $values['Public'] = $copyMe->m_data['Public'];
            $values['ArticleOrder'] = $articleOrder;
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
            if (is_null($articleOrder)) {
                $g_ado_db->Execute('LOCK TABLES Articles WRITE');
                $articleOrder = $g_ado_db->GetOne('SELECT MAX(ArticleOrder) + 1 FROM Articles');
                $articleCopy->setProperty('ArticleOrder', $articleOrder);
                $g_ado_db->Execute('UNLOCK TABLES');
            }

            // Insert an entry into the article type table.
            $newArticleData = new ArticleData($articleCopy->m_data['Type'],
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
            $languageObj = new Language($copyMe->getLanguageId());
            $logtext .= getGS('Article copied to Article #$4 (publication $5, issue $6, section $7).',
                $articleCopy->getArticleNumber(), $articleCopy->getPublicationId(),
                $articleCopy->getIssueNumber(), $articleCopy->getSectionNumber());
        }

        Log::ArticleMessage($copyMe, $logtext, null, 155);
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
    public function move($p_destPublicationId = 0, $p_destIssueNumber = 0,
                         $p_destSectionNumber = 0)
    {
        global $g_ado_db;

        $columns = array();
        if ($this->m_data["IdPublication"] != $p_destPublicationId) {
            $columns["IdPublication"] = (int)$p_destPublicationId;
        }
        if ($this->m_data["NrIssue"] != $p_destIssueNumber) {
            $columns["NrIssue"] = (int)$p_destIssueNumber;
        }
        if ($this->m_data["NrSection"] != $p_destSectionNumber) {
            $columns["NrSection"] = (int)$p_destSectionNumber;
        }
        $success = false;
        if (count($columns) > 0) {
            $success = $this->update($columns);
            if ($success) {
            	$this->setWorkflowStatus($this->getWorkflowStatus());
                $g_ado_db->Execute('LOCK TABLES Articles WRITE');
                $articleOrder = $g_ado_db->GetOne('SELECT MAX(ArticleOrder) + 1 FROM Articles');
                $this->setProperty('ArticleOrder', $articleOrder);
                $g_ado_db->Execute('UNLOCK TABLES');
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
    public function getUniqueName($p_currentName)
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
    public function createTranslation($p_languageId, $p_userId, $p_name)
    {
        // Construct the duplicate article object.
        $articleCopy = new Article();
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
        $articleCopyData = new ArticleData($articleCopy->m_data['Type'],
            $articleCopy->m_data['Number'], $articleCopy->m_data['IdLanguage']);
        $articleCopyData->create();

        $origArticleData = $this->getArticleData();
        $origArticleData->copyToExistingRecord($articleCopy->getArticleNumber(), $p_languageId);

        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article translated to "$4" ($5)',
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::ArticleMessage($this, $logtext, null, 31);

        // geo-map processing
        Geo_Map::OnCreateTranslation($this->m_data['Number'], $this->m_data['IdLanguage'], $p_languageId);

        return $articleCopy;
    } // fn createTranslation


    /**
     * Delete article from database.  This will
     * only delete one specific translation of the article.
     *
     * @return boolean
     */
    public function delete()
    {
        // It is an optimization to put these here because in most cases
        // you dont need these files.
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTopic.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleIndex.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticlePublish.php');

        // Delete scheduled publishing
        ArticlePublish::OnArticleDelete($this->m_data['Number'], $this->m_data['IdLanguage']);

        // Delete Article Comments
        // @todo change this with DOCTRINE2 CASCADE DELETE
        global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
        $repository->deleteArticle($this->m_data['Number'], $this->m_data['IdLanguage']);
        $repository->flush();

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

        // geo-map processing
        // is this the last translation?
        if (count($this->getLanguages()) <= 1) {
            // unlink the article-map pointers
            Geo_Map::OnArticleDelete($this->m_data['Number']);
        }
        else {
            // removing non-last translation of the map poi contents
            Geo_Map::OnLanguageDelete($this->m_data['Number'], $this->m_data['IdLanguage']);
        }

        // Delete row from article type table.
        $articleData = new ArticleData($this->m_data['Type'],
            $this->m_data['Number'],
            $this->m_data['IdLanguage']);
        $articleData->delete();

        $tmpObj = clone $this; // for log
        $tmpData = $this->m_data;
        $tmpData['languageName'] = $this->getLanguageName();
        // Delete row from Articles table.
        $deleted = parent::delete();

        if ($deleted) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            Log::ArticleMessage($tmpObj, getGS('Article deleted.'), null, 32);
        }
        $this->m_cacheUpdate = true;
        return $deleted;
    } // fn delete


    /**
     * Get the time the article was locked.
     *
     * @return string
     *		In the form of YYYY-MM-DD HH:MM:SS
     */
    public function getLockTime()
    {
        return $this->m_data['LockTime'];
    } // fn getLockTime


    /**
     * Return TRUE if the article is locked, FALSE if it isnt.
     * @return boolean
     */
    public function isLocked()
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
    public function setIsLocked($p_lock, $p_userId = null)
    {
        // Check parameters
        if ($p_lock && !is_numeric($p_userId)) {
            return;
        }

        // Don't change the article timestamp when the
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
     * language the article is written in.
     *
     * @param boolean $p_excludeCurrent
     *      If true, exclude the current language from the list.
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bynumber, byname, byenglish_name, bycode
     *      order_direction can take one of the following values:
     *        asc, desc
     * @return array
     */
    public function getLanguages($p_excludeCurrent = false, array $p_order = array(),
    $p_published = false)
    {
        if (!$this->exists()) {
            return array();
        }
        $tmpLanguage = new Language();
        $columnNames = $tmpLanguage->getColumnNames(true);
         $queryStr = 'SELECT '.implode(',', $columnNames).' FROM Articles, Languages '
                     .' WHERE Articles.IdLanguage = Languages.Id'
                    .' AND IdPublication = ' . $this->m_data['IdPublication']
                     .' AND NrIssue = ' . $this->m_data['NrIssue']
                     .' AND NrSection = ' . $this->m_data['NrSection']
                     .' AND Number = ' . $this->m_data['Number'];
        if ($p_excludeCurrent) {
            $queryStr .= ' AND Languages.Id != ' . $this->m_data['IdLanguage'];
         }
         if ($p_published) {
             $queryStr .= " AND Articles.Published = 'Y'";
         }
        $order = Article::ProcessLanguageListOrder($p_order);
        foreach ($order as $orderDesc) {
            $sqlOrder[] = $orderDesc['field'] . ' ' . $orderDesc['dir'];
        }
         if (count($sqlOrder) > 0) {
             $queryStr .= ' ORDER BY ' . implode(', ', $sqlOrder);
         }
         $languages = DbObjectArray::Create('Language', $queryStr);
        return $languages;
    } // fn getLanguages


    /**
     * Return an array of Article objects, one for each
     * type of language the article is written in.
     *
     * @param int $p_articleNumber
     * 		Optional. Use this if you call this function statically.
     *
     * @return array
     */
    public function getTranslations($p_articleNumber = null)
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
    public function getLanguageName()
    {
        if (is_null($this->m_languageName)) {
            $language = new Language($this->m_data['IdLanguage']);
            $this->m_languageName = $language->getNativeName();
        }
        return $this->m_languageName;
    } // fn getLanguageName


    /**
     * Get the section that this article is in.
     * @return object
     */
    public function getSection()
    {
        $section = new Section($this->getPublicationId(), $this->getIssueNumber(),
        $this->getLanguageId(), $this->getSectionNumber());
        if (!$section->exists()) {
            $sections = Section::GetSections($this->getPublicationId(), $this->getIssueNumber());
            if (count($sections) > 0) {
                return $sections[0];
            }
        }
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
    public function positionRelative($p_direction, $p_spacesToMove = 1)
    {
        global $g_ado_db;

        CampCache::singleton()->clear('user');
        $this->fetch();

        $g_ado_db->Execute('LOCK TABLES Articles WRITE');

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
                $g_ado_db->Execute('UNLOCK TABLES');
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

        $g_ado_db->Execute('UNLOCK TABLES');

        CampCache::singleton()->clear('user');
        $this->m_cacheUpdate = true;

        // Re-fetch this article to get the updated article order.
        $this->fetch();
        return true;
    } // fn positionRelative


    /**
     * Move the article to the given position (i.e. reorder the article).
     * @param int $p_moveToPosition
     * @return boolean
     */
    public function positionAbsolute($p_moveToPosition = 1)
    {
        global $g_ado_db;

        CampCache::singleton()->clear('user');
        $this->fetch();

        $g_ado_db->Execute('LOCK TABLES Articles WRITE');

        // Get the article that is in the location we are moving
        // this one to.
        $queryStr = 'SELECT Number, IdLanguage, ArticleOrder FROM Articles '
                    .' WHERE IdPublication='.$this->m_data['IdPublication']
                    .' AND NrIssue='.$this->m_data['NrIssue']
                    .' AND NrSection='.$this->m_data['NrSection']
                     .' ORDER BY ArticleOrder ASC LIMIT '.($p_moveToPosition - 1).', 1';
        $destRow = $g_ado_db->GetRow($queryStr);
        if (!$destRow) {
            $g_ado_db->Execute('UNLOCK TABLES');
            return false;
        }
        if ($destRow['ArticleOrder'] == $this->m_data['ArticleOrder']) {
            $g_ado_db->Execute('UNLOCK TABLES');

            // Move the destination down one.
            $destArticle = new Article($destRow['IdLanguage'], $destRow['Number']);
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

        $g_ado_db->Execute('UNLOCK TABLES');

        CampCache::singleton()->clear('user');
        $this->m_cacheUpdate = true;

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
    public function userCanModify($p_user)
    {
        $userCreatedArticle = ($this->m_data['IdUser'] == $p_user->getUserId());
        $articleIsNew = ($this->m_data['Published'] == 'N');
        $articleIsNotPublished = (($this->m_data['Published'] == 'N') || ($this->m_data['Published'] == 'S'));

        if ($p_user->hasPermission('Publish')
                || ($p_user->hasPermission('ChangeArticle')
                && $articleIsNotPublished)
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
    public function getArticleTypeTableName()
    {
        return 'X'.$this->m_data['Type'];
    } // fn getArticleTypeTableName


    /**
     * Get the publication ID of the publication that contains this article.
     * @return int
     */
    public function getPublicationId()
    {
        return (int)$this->m_data['IdPublication'];
    } // fn getPublicationId


    /**
     * Set the publication ID.
     *
     * @param int $p_value
     * @return boolean
     */
    public function setPublicationId($p_value)
    {
        if (is_numeric($p_value)) {
            return $this->setProperty('IdPublication', (int)$p_value);
        } else {
            return false;
        }
    } // fn setPublicationId


    /**
     * Get the issue that the article resides within.
     *
     * @return int
     */
    public function getIssueNumber()
    {
        return (int)$this->m_data['NrIssue'];
    } // fn getIssueNumber


    /**
     * Set the issue number.
     *
     * @param int $p_value
     * @return boolean
     */
    public function setIssueNumber($p_value)
    {
        if (is_numeric($p_value)) {
            $this->m_cacheUpdate = true;
            return $this->setProperty('NrIssue', (int)$p_value);
        } else {
            return false;
        }
    } // fn setIssueNumber


    /**
     * Get the section number that contains this article.
     *
     * @return int
     */
    public function getSectionNumber()
    {
        return (int)$this->m_data['NrSection'];
    } // fn getSectionNumber


    /**
     * Set the section number.
     *
     * @param int $p_value
     * @return boolean
     */
    public function setSectionNumber($p_value)
    {
        if (is_numeric($p_value)) {
            $this->m_cacheUpdate = true;
            return $this->setProperty('NrSection', (int)$p_value);
        } else {
            return false;
        }
    } // fn setSectionNumber


    /**
     * Return the language the article was written in.
     *
     * @return int
     */
    public function getLanguageId()
    {
        return (int)$this->m_data['IdLanguage'];
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
    public function getArticleNumber()
    {
        return (int)$this->m_data['Number'];
    } // fn getArticleNumber


    /**
     * Get the title of the article.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->m_data['Name'];
    } // fn getTitle


    /**
     * Alias for getTitle().
     *
     * @return string
     */
    public function getName()
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
    public function setTitle($p_title)
    {
        $this->m_cacheUpdate = true;
        return parent::setProperty('Name', $p_title);
    } // fn setTitle


    /**
     * Get the article type.
     * @return string
     */
    public function getType()
    {
        return $this->m_data['Type'];
    } // fn getType


    /**
     * Get the logged in language's translation of the article type.
     * @return string
     */
    public function getTranslateType($p_languageId = null)
    {
        $type = $this->getType();
        $typeObj = new ArticleType($type);
        return $typeObj->getDisplayName($p_languageId);
    }


    /**
     * Return the user ID of the user who created this article.
     * @return int
     */
    public function getCreatorId()
    {
        return (int)$this->m_data['IdUser'];
    } // fn getCreatorId


    /**
     * Set the user ID of the user who created this article.
     *
     * @param int $p_value
     * @return boolean
     */
    public function setCreatorId($p_value)
    {
        return parent::setProperty('IdUser', (int)$p_value);
    } // fn setCreatorId


    /**
     * Set the ID of the author who wrote this article.
     *
     * @param int $p_value
     * @return boolean
     */
    public function setAuthor(Author $p_author)
    {
        $defaultAuthorType = $p_author->setType();
        // Links the author to the article
        $articleAuthorObj = new ArticleAuthor($this->getArticleNumber(),
                                              $this->getLanguageId(),
                                              $p_author->getId(), $defaultAuthorType);
        if (!$articleAuthorObj->exists()) {
            $articleAuthorObj->create();
        }
    } // fn setAuthor


    /**
     * Return an integer representing the order of the article
     * within the section.  Note that these numbers are not sequential
     * and can only be compared with the other articles in the section.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->m_data['ArticleOrder'];
    } // fn getOrder


    /**
     * Return true if the article will appear on the front page.
     *
     * @return boolean
     */
    public function onFrontPage()
    {
        return ($this->m_data['OnFrontPage'] == 'Y');
    } // fn onFrontPage


    /**
     * Set whether the article should appear on the front page.
     *
     * @param boolean $p_value
     * @return boolean
     */
    public function setOnFrontPage($p_value)
    {
        $this->m_cacheUpdate = true;
        return parent::setProperty('OnFrontPage', $p_value?'Y':'N');
    } // fn setOnFrontPage


    /**
     * Return TRUE if this article will appear on the section page.
     *
     * @return boolean
     */
    public function onSectionPage()
    {
        return ($this->m_data['OnSection'] == 'Y');
    } // fn onSectionPage


    /**
     * Set whether the article will appear on the section page.
     * @param boolean $p_value
     * @return boolean
     */
    public function setOnSectionPage($p_value)
    {
        $this->m_cacheUpdate = true;
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
    public function getWorkflowStatus()
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
    public function getWorkflowDisplayString($p_value = null)
    {
        if (is_null($p_value)) {
            $p_value = $this->m_data['Published'];
        }
        if ( ($p_value != 'Y') && ($p_value != 'S') && ($p_value != 'N') && $p_value != 'M') {
            return '';
        }
        switch ($p_value) {
        case 'Y':
            return getGS("Published");
        case 'M':
            return getGS('Publish with issue');
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
    public function setWorkflowStatus($p_value)
    {
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleIndex.php');

        $p_value = strtoupper($p_value);
        if ( ($p_value != 'Y') && ($p_value != 'S') && ($p_value != 'N') && ($p_value != 'M')) {
            return false;
        }

        // If the article is being published
        if ( ($this->getWorkflowStatus() != 'Y') && ($p_value == 'Y') ) {
            $this->setProperty('PublishDate', 'NOW()', true, true);
        }
        // Unlock the article if it changes status.
        if ( $this->getWorkflowStatus() != $p_value ) {
            $this->setIsLocked(false);
        }

        if ($p_value == 'Y' || $p_value == 'M') {
            $issueObj = new Issue($this->getPublicationId(), $this->getLanguageId(),
            $this->getIssueNumber());
            if (!$issueObj->exists()) {
                return false;
            }
            $p_value = $issueObj->isPublished() ? 'Y' : 'M';
        }

        $oldStatus = $this->getWorkflowStatus();

        if (!parent::setProperty('Published', $p_value)) {
            return false;
        }

        CampCache::singleton()->clear('user');
        $this->m_cacheUpdate = true;

        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article status changed from $1 to $2.',
            $this->getWorkflowDisplayString($oldStatus), $this->getWorkflowDisplayString($p_value));
        Log::ArticleMessage($this, $logtext, null, 35);
        return true;
    } // fn setWorkflowStatus


    /**
     * Get the date the article was published.
     * @return string
     */
    public function getPublishDate()
    {
        return $this->m_data['PublishDate'];
    } // fn getPublishDate


    /**
     * Set the date the article was published, parameter must be in the
     * form YYYY-MM-DD.
     * @param string $p_value
     * @return boolean
     */
    public function setPublishDate($p_value)
    {
        return $this->setProperty('PublishDate', $p_value);
    } // fn setPublishDate


    /**
     * Return the date the article was created in the
     * form YYYY-MM-DD HH:MM:SS.
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->m_data['UploadDate'];
    } // fn getCreationDate


    /**
     * Set the date the article was created, parameter must be in the
     * form YYYY-MM-DD.
     * @param string $p_value
     * @return boolean
     */
    public function setCreationDate($p_value)
    {
        return $this->setProperty('UploadDate', $p_value);
    } // fn setCreationDate


    /**
     * Return the date the article was last modified in the
     * form YYYY-MM-DD HH:MM:SS.
     *
     * @return string
     */
    public function getLastModified()
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
    public function getKeywords()
    {
        require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
        $keywords = $this->m_data['Keywords'];
        $keywordSeparator = SystemPref::Get("KeywordSeparator");
        return str_replace(",", $keywordSeparator, $keywords);
    } // fn getKeywords


    public function getReads() {
        if (!$this->exists()) {
            return null;
        }
        if (empty($this->m_data['object_id'])) {
            return 0;
        }
        $requestObject = new RequestObject($this->m_data['object_id']);
        return $requestObject->getRequestCount();
    }


    /**
     * @param string $p_value
     * @return boolean
     */
    public function setKeywords($p_value)
    {
        require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
        $keywordsSeparator = SystemPref::Get('KeywordSeparator');
        $p_value = str_replace($keywordsSeparator, ",", $p_value);
        CampCache::singleton()->clear('user');

        return parent::setProperty('Keywords', $p_value);
    } // fn setKeywords


    /**
     * Return TRUE if this article was published.
     *
     * @return boolean
     */
    public function isPublished()
    {
        return ($this->m_data['Published'] == 'Y');
    } // fn isPublic


    /**
     * Return TRUE if this article is viewable by non-subscribers.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return ($this->m_data['Public'] == 'Y');
    } // fn isPublic


    /**
     * Set whether this article is viewable by non-subscribers.
     *
     * @param boolean $p_value
     * @return boolean
     */
    public function setIsPublic($p_value)
    {
        return parent::setProperty('Public', $p_value?'Y':'N');
    } // fn setIsPublic


    /**
     * @return boolean
     */
    public function isIndexed()
    {
        return ($this->m_data['IsIndexed'] == 'Y');
    } // fn isIndexed


    /**
     * @param boolean value
     */
    public function setIsIndexed($p_value)
    {
        return parent::setProperty('IsIndexed', $p_value?'Y':'N');
    } // fn setIsIndexed


    /**
     * Return the user ID of the user who has locked the article.
     * @return int
     */
    public function getLockedByUser()
    {
        return $this->m_data['LockUser'];
    } // fn getLockedByUser


    /**
     * Get the URL name for this article.
     *
     * @return string
     */
    public function getUrlName()
    {
        return $this->m_data['ShortName'];
    } // fn getUrlName


    /**
     * @param string value
     */
    public function setUrlName($p_value)
    {
        $this->m_cacheUpdate = true;
        return parent::setProperty('ShortName', $p_value);
    } // fn setUrlName


    /**
     * Return the ArticleData object for this article.
     *
     * @return ArticleData
     */
    public function getArticleData()
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
    public function commentsEnabled()
    {
        return $this->m_data['comments_enabled'];
    } // fn commentsEnabled


    /**
     * Set whether comments are enabled for this article.
     *
     * @param boolean $p_value
     * @return boolean
     */
    public function setCommentsEnabled($p_value)
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
    public function commentsLocked()
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
    public function setCommentsLocked($p_value)
    {
        $p_value = $p_value ? '1' : '0';
        return $this->setProperty('comments_locked', $p_value);
    } // fn setCommentsLocked


    /**
     * @return GeoMap
     */
    public function getMap()
    {
        return Geo_Map::GetMapByArticle($this->getArticleNumber());
    }

    /*****************************************************************/
    /** Static Functions                                             */
    /*****************************************************************/


    /**
     * Set the article workflow on issue status change. Articles to be
     * published with the issue will be published on article publish.
     * Published articles are set to "publish with issue" on issue
     * unpublish.
     *
     * @param int $p_publicationId
     * @param int $p_languageId
     * @param int $p_issueNo
     * @param int $p_publish
     */
    public static function OnIssuePublish($p_publicationId, $p_languageId,
    $p_issueNo, $p_publish = true)
    {
        global $g_ado_db;

        settype($p_publicationId, 'integer');
        settype($p_languageId, 'integer');
        settype($p_issueNo, 'integer');

        $issueObj = new Issue($p_publicationId, $p_languageId, $p_issueNo);
        if (!$issueObj->exists()) {
            return false;
        }

        if (($issueObj->isPublished() && $p_publish)
        || (!$issueObj->isPublished() && !$p_publish)) {
            return false;
        }

        $fromState = $p_publish ? 'M' : 'Y';
        $toState = $p_publish ? 'Y' : 'M';

        $sql = "UPDATE Articles SET Published = '$toState' WHERE "
        . "IdPublication = $p_publicationId AND IdLanguage = $p_languageId"
        . " AND NrIssue = $p_issueNo AND Published = '$fromState'";
        $res = $g_ado_db->Execute($sql);

        CampCache::singleton()->clear('user');
        if (CampTemplateCache::factory()) {
            CampTemplateCache::factory()->update(array(
                'language' => $p_languageId,
                'publication' => $p_publicationId,
                'issue' => $p_issueNo,
                'section' => null,
                'article' => null,
            ));
        }
        return $res;
    }


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
    public static function GetByNumber($p_articleNr, $p_publicationId, $p_issueNr,
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
    public static function GetByName($p_name, $p_publicationId = null, $p_issueId = null,
                                     $p_sectionId = null, $p_languageId = null, $p_skipCache = false)
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
        $whereClause[] = "Name='" . $g_ado_db->escape($p_name) . "'";
        if (count($whereClause) > 0) {
            $queryStr .= ' WHERE ' . implode(' AND ', $whereClause);
        }

        if (!$p_skipCache && CampCache::IsEnabled()) {
            $paramsArray['get_by_name_where_clause'] = serialize($whereClause);
            $cacheListObj = new CampCacheList($paramsArray, __METHOD__);
            $articlesList = $cacheListObj->fetchFromCache();
            if ($articlesList !== false && is_array($articlesList)) {
                return $articlesList;
            }
        }

        $articlesList = DbObjectArray::Create("Article", $queryStr);

        // stores articles list in cache
        if (!$p_skipCache && CampCache::IsEnabled()) {
            $cacheListObj->storeInCache($articlesList);
        }

        return $articlesList;
    } // fn GetByName


    /**
     * Return the number of unique (language-independant) articles
     * according to the given parameters.
     * @param int $p_publicationId
     * @param int $p_issueId
     * @param int $p_sectionId
     * @return int
     */
    public static function GetNumUniqueArticles($p_publicationId = null, $p_issueId = null,
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
    public static function GetArticlesByUser($p_userId, $p_start = 0, $p_upperLimit = 20)
    {
        global $g_ado_db;
        $queryStr = 'SELECT * FROM Articles '
                    ." WHERE IdUser=$p_userId"
                    .' ORDER BY Number DESC, IdLanguage '
                    ." LIMIT $p_start, $p_upperLimit";
        $query = $g_ado_db->Execute($queryStr);
        $articles = array();
        while ($row = $query->FetchRow()) {
            $tmpArticle = new Article();
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
    public static function GetSubmittedArticles($p_start = 0, $p_upperLimit = 20)
    {
        global $g_ado_db;
        $tmpArticle = new Article();
        $columnNames = $tmpArticle->getColumnNames(true);
        $queryStr = 'SELECT '.implode(", ", $columnNames)
                    .' FROM Articles '
                    ." WHERE Published = 'S' "
                    .' ORDER BY Number DESC, IdLanguage '
                    ." LIMIT $p_start, $p_upperLimit";
        $query = $g_ado_db->Execute($queryStr);
        $articles = array();
        if ($query != false) {
            while ($row = $query->FetchRow()) {
                $tmpArticle = new Article();
            $tmpArticle->fetch($row);
            $articles[] = $tmpArticle;
            }
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
    public static function GetUnplacedArticles($p_start = 0, $p_maxRows = 20)
    {
        global $g_ado_db;
        $tmpArticle = new Article();
        $columnNames = $tmpArticle->getColumnNames(true);
        $queryStr = 'SELECT '.implode(", ", $columnNames)
        .' FROM Articles '
        ." WHERE IdPublication=0 AND NrIssue=0 AND NrSection=0 "
        .' ORDER BY Number DESC, IdLanguage '
        ." LIMIT $p_start, $p_maxRows";
        $query = $g_ado_db->Execute($queryStr);
        $articles = array();
        if ($query != false) {
            while ($row = $query->FetchRow()) {
                $tmpArticle = new Article();
                $tmpArticle->fetch($row);
                $articles[] = $tmpArticle;
            }
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
    public static function GetAllLanguages()
    {
        $tmpLanguage = new Language();
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
    public static function GetArticles($p_publicationId = null, $p_issueNumber = null,
                                       $p_sectionNumber = null, $p_languageId = null,
                                       $p_sqlOptions = null, $p_countOnly = false,
                                       $p_whereOptions = null)
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
        if (!is_null($p_whereOptions) && is_array($p_whereOptions)) {
            foreach ($p_whereOptions as $key => $value) {
                $whereClause[] = $value;
            }
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
    public static function GetArticlesGrouped($p_publicationId = null,
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
    public static function GetNumArticlesOfType($p_type)
    {
        $articleType = new ArticleType($p_type);
        if (!$articleType->exists()) {
            return false;
        }
        return $articleType->getNumArticles();
    } // fn GetNumArticlesOfType


    /**
     * Return an array of article objects of a certain type.
     *
     * @param string p_type
     *
     * @return array
     */
    public static function GetArticlesOfType($p_type)
    {
        global $g_ado_db;
        $sql = "SELECT * FROM Articles WHERE Type = '"
        . $g_ado_db->escape($p_type) . "'";
        $articles = DbObjectArray::Create('Article', $sql);
        return $articles;
    } // fn GetArticlesOfType


    /**
     * Get the $p_max number of the most recently published articles.
     * @param int $p_max
     * @return array
     */
    public static function GetRecentArticles($p_max)
    {
        $queryStr = "SELECT * FROM Articles "
                   ." WHERE Published='Y'"
                   ." ORDER BY PublishDate DESC"
                   ." LIMIT $p_max";
        $result = DbObjectArray::Create('Article', $queryStr);
        return $result;
    } // fn GetRecentArticles


    /**
     * Get the $p_max number of the most recently modified articles.
     * @param int $p_max
     * @return array
     */
    public static function GetRecentlyModifiedArticles($p_max)
    {
        $queryStr = "SELECT * FROM Articles "
                   ." ORDER BY time_updated DESC"
                   ." LIMIT $p_max";
        $result = DbObjectArray::Create('Article', $queryStr);
        return $result;
    } // fn GetRecentlyModifiedArticles


    /**
     * @param int $p_publicationId
     *
     * @param int $p_languageId
     *
     *
     * @return mixed
     *      array of issue publication dates
     *      null if query does not match any issue
     */
    public static function GetPublicationDates($p_publicationId,
                           $p_languageId,
                           $p_skipCache = false)
    {
        global $g_ado_db;
        $queryStr = 'SELECT Number FROM Articles '
            . 'WHERE IdPublication = ' . $p_publicationId . ' AND '
            . 'IdLanguage = ' . $p_languageId . " AND Published = 'Y' "
            . 'GROUP BY PublishDate ORDER BY PublishDate';
        $rows = $g_ado_db->GetAll($queryStr);

        $dates = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
            $tmpObj = new Article($p_languageId, $row['Number']);
            if ($tmpObj->exists()) {
                $dates[] = $tmpObj->getPublishDate();
            }
            }
        }
        if (empty($dates)) {
            return null;
        }

        return array_unique($dates);
    } // fn GetPublicationDates


    /**
     * Unlock all articles by the given user.
     * @param int $p_userId
     * @return void
     */
    public static function UnlockByUser($p_userId)
    {
        global $g_ado_db;
        $queryStr = 'UPDATE Articles SET LockUser=0, LockTime=0, time_updated=time_updated'
                    ." WHERE LockUser=$p_userId";
        $g_ado_db->Execute($queryStr);
    } // fn UnlockByUser


    /**
     * Returns an articles list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     * @param integer $p_count
     *    The total count of the elements; this count is computed without
     *    applying the start ($p_start) and limit parameters ($p_limit)
     *
     * @return array $articlesList
     *    An array of Article objects
     */
    public static function GetList(array $p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        if (!$p_skipCache && CampCache::IsEnabled()) {
            $paramsArray['parameters'] = serialize($p_parameters);
            $paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
            $paramsArray['start'] = $p_start;
            $paramsArray['limit'] = $p_limit;
            $cacheListObj = new CampCacheList($paramsArray, __METHOD__);
            $articlesList = $cacheListObj->fetchFromCache();
            if ($articlesList !== false && is_array($articlesList)) {
                return $articlesList;
            }
        }

        $matchAllTopics = false;
        $hasTopics = array();
        $hasNotTopics = array();
        $selectClauseObj = new SQLSelectClause();
        $otherTables = array();

        // sets the name of the table for the this database object
        $tmpArticle = new Article();
        $articleTable = $tmpArticle->getDbTableName();
        $selectClauseObj->setTable($articleTable);
        unset($tmpArticle);

        $languageId = null;

        // parses the given parameters in order to build the WHERE part of
        // the SQL SELECT sentence
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param, $otherTables);
            $leftOperand = strtolower($comparisonOperation['left']);
            if ($leftOperand == 'idlanguage' && $comparisonOperation['symbol'] == '=') {
                $languageId = $comparisonOperation['right'];
            }

            if (array_key_exists($leftOperand, Article::$s_regularParameters)) {
                // regular article field, having a direct correspondent in the
                // Article table fields
                $whereCondition = Article::$s_regularParameters[$leftOperand]
                    . ' ' . $comparisonOperation['symbol']
                    . " '" . $g_ado_db->escape($comparisonOperation['right']) . "' ";
                if ($leftOperand == 'reads'
                && strstr($comparisonOperation['symbol'], '=') !== false
                && $comparisonOperation['right'] == 0) {
                    $selectClauseObj->addConditionalWhere($whereCondition);
                    $isNullCond = Article::$s_regularParameters[$leftOperand]
                                . ' IS NULL';
                    $selectClauseObj->addConditionalWhere($isNullCond);
                } elseif ($leftOperand == 'type' && $comparisonOperation['symbol'] == '=' ) {
					$selectClauseObj->addConditionalWhere($whereCondition);
                } else {
                	$selectClauseObj->addWhere($whereCondition);
                }
            } elseif ($leftOperand == 'matchalltopics') {
                // set the matchAllTopics flag
                $matchAllTopics = true;
            } elseif ($leftOperand == 'topic') {
                // add the topic to the list of match/do not match topics depending
                // on the operator
                $topic = new Topic($comparisonOperation['right']);
                if ($topic->exists()) {
                    $topicIds = $topic->getSubtopics(true, 0);
                    $topicIds[] = $comparisonOperation['right'];
                    if ($comparisonOperation['symbol'] == '=') {
                        $hasTopics[] = $topicIds;
                    } else {
                        $hasNotTopics[] = $topicIds;
                    }
                }
            } elseif ($leftOperand == 'author') {
                $otherTables['ArticleAuthors'] = array('__JOIN' => ',');
                $author = Author::ReadName($comparisonOperation['right']);
                $symbol = $comparisonOperation['symbol'];
                $valModifier = strtolower($symbol) == 'like' ? '%' : '';

                $firstName = $g_ado_db->escape($author['first_name']);
                $lastName = $g_ado_db->escape($author['last_name']);
                $whereCondition = "ArticleAuthors.fk_author_id IN
                    (SELECT Authors.id
                     FROM Authors
                     WHERE CONCAT(Authors.first_name, ' ', Authors.last_name) $symbol
                         '$valModifier$firstName $lastName$valModifier')";
                $selectClauseObj->addWhere($whereCondition);
                $selectClauseObj->addWhere('Articles.Number = ArticleAuthors.fk_article_number');
                $selectClauseObj->addWhere('Articles.IdLanguage = ArticleAuthors.fk_language_id');
            } elseif ($leftOperand == 'search_phrase') {
                $searchQuery = ArticleIndex::SearchQuery($comparisonOperation['right']);
                $mainClauseConstraint = "(Articles.Number, Articles.IdLanguage) IN ( $searchQuery )";
                $selectClauseObj->addWhere($mainClauseConstraint);
            } elseif ($leftOperand == 'location') {
                $num = '[-+]?[0-9]+(?:\.[0-9]+)?';
                if (preg_match("/($num) ($num), ($num) ($num)/",
                    trim($comparisonOperation['right']), $matches)) {
                    $queryLocation = Geo_Map::GetGeoSearchSQLQuery(array(
                        array(
                            'latitude' => $matches[1],
                            'longitude' => $matches[2],
                        ), array(
                            'latitude' => $matches[3],
                            'longitude' => $matches[4],
                        ),
                    ));
                    $selectClauseObj->addWhere("Articles.Number IN ($queryLocation)");
                }
            } else {
                // custom article field; has a correspondence in the X[type]
                // table fields
                $sqlQuery = self::ProcessCustomField($comparisonOperation, $languageId);
                if (!is_null($sqlQuery)) {
                    $whereCondition = "Articles.Number IN (\n$sqlQuery        )";
                    $selectClauseObj->addWhere($whereCondition);
                }
            }
        }

        if (count($hasTopics) > 0 || count($hasNotTopics) > 0) {
            $typeAttributes = ArticleTypeField::FetchFields(null, null, 'topic', false,
            false, false, true, $p_skipCache);
        }
        if (count($hasTopics) > 0) {
            if ($matchAllTopics) {
                foreach ($hasTopics as $topicId) {
                    $sqlQuery = Article::BuildTopicSelectClause($topicId, $typeAttributes);
                    $whereCondition = "Articles.Number IN (\n$sqlQuery        )";
                    $selectClauseObj->addWhere($whereCondition);
                }
            } else {
                $sqlQuery = Article::BuildTopicSelectClause($hasTopics, $typeAttributes);
                $whereCondition = "Articles.Number IN (\n$sqlQuery        )";
                $selectClauseObj->addWhere($whereCondition);
            }
        }
        if (count($hasNotTopics) > 0) {
            $sqlQuery = Article::BuildTopicSelectClause($hasNotTopics, $typeAttributes);
            $whereCondition = "Articles.Number NOT IN (\n$sqlQuery        )";
            $selectClauseObj->addWhere($whereCondition);
        }

        // create the count clause object
        $countClauseObj = clone $selectClauseObj;

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the ORDER BY condition
        $p_order = array_merge($p_order, Article::$s_defaultOrder);
        $order = Article::ProcessListOrder($p_order, $otherTables, $otherWhereConditions);
        foreach ($order as $orderDesc) {
            $orderColumn = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }
        if (count($otherTables) > 0) {
            foreach ($otherTables as $table=>$fields) {
            	$joinType = 'LEFT JOIN';
            	if (isset($fields['__JOIN'])) {
            		$joinType = $fields['__JOIN'];
            	}
                if (isset($fields['__TABLE_ALIAS'])) {
                    $tableAlias = $fields['__TABLE_ALIAS'];
                    $tableJoin = "\n    $joinType $table AS $tableAlias \n";
                } else {
                    $tableAlias = $table;
                    $tableJoin = "\n    $joinType $tableAlias \n";
                }
                $firstCondition = true;
                foreach ($fields as $parent=>$child) {
                    if ($parent == '__TABLE_ALIAS' || $parent == '__JOIN') {
                        continue;
                    }
                    $condOperator = $firstCondition ? ' ON ' : 'AND ';
                    if ($parent == '__CONST') {
                        $constTable = $child['table'];
                        $constField = $child['field'];
                        $value = $child['value'];
                        $negate = isset($child['negate']) ? $child['negate'] : false;
                        if (is_null($value)) {
                            $operator = $negate ? 'IS NOT' : 'IS';
                            $value = 'NULL';
                        } else {
                            $operator = $negate ? '!=' : '=';
                            $value = "'" . $g_ado_db->escape($value) . "'";
                        }
                        $tableJoin .= "        $condOperator`$constTable`.`$constField` $operator $value\n";
                    } else {
                        $tableJoin .= "        $condOperator`$articleTable`.`$parent` = `$tableAlias`.`$child`\n";
                    }
                    $firstCondition = false;
                }
                $selectClauseObj->addJoin($tableJoin);
                $countClauseObj->addJoin($tableJoin);
            }
        }
        // other where conditions needed for certain order options
        foreach ($otherWhereConditions as $whereCondition) {
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // gets the column list to be retrieved for the database table
        $selectClauseObj->addColumn('Articles.Number');
        $selectClauseObj->addColumn('Articles.IdLanguage');
        $countClauseObj->addColumn('COUNT(*)');

        // sets the LIMIT start and offset values
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the SQL query
        $selectQuery = $selectClauseObj->buildQuery();
        $countQuery = $countClauseObj->buildQuery();

        // runs the SQL query
        $articles = $g_ado_db->GetAll($selectQuery);
        if (is_array($articles)) {
            $p_count = $g_ado_db->GetOne($countQuery);

            // builds the array of Article objects
            $articlesList = array();
            foreach ($articles as $article) {
                $articlesList[] = new Article($article['IdLanguage'], $article['Number']);
            }
        } else {
            $articlesList = array();
            $p_count = 0;
        }

        // stores articles list in cache
        if (!$p_skipCache && CampCache::IsEnabled()) {
            $cacheListObj->storeInCache($articlesList);
        }

        return $articlesList;
    } // fn GetList

    /**
     * Get total articles count in db.
     *
     * @return int
     */
    public static function GetTotalCount()
    {
        global $g_ado_db;

        $sql = 'SELECT COUNT(*) FROM Articles';
        return $g_ado_db->GetOne($sql);
    }

    private static function ProcessCustomField(array $p_comparisonOperation, $p_languageId = null)
    {
        global $g_ado_db;

        $fieldName = $p_comparisonOperation['left'];
        $fieldParts = preg_split('/\./', $fieldName);
        if (count($fieldParts) > 1) {
            $fieldName = $fieldParts[1];
            $articleType = $fieldParts[0];
            $field = new ArticleTypeField($articleType, $fieldName);
            if (!$field->exists()) {
                return null;
            }
            $fields = array($field);
        } else {
            $articleType = null;
            $fields = ArticleTypeField::FetchFields($fieldName, $articleType,
            null, false, false, false, true, true);
            if (count($fields) == 0) {
                return null;
            }
        }
        $queries = array();
        foreach ($fields as $fieldObj) {
            $query .= '        SELECT NrArticle FROM `X' . $fieldObj->getArticleType()
                   . '` WHERE ' . $fieldObj->getName() . ' '
                   . $p_comparisonOperation['symbol']
                   . " '" . $g_ado_db->escape($p_comparisonOperation['right']) . "'";
            if (!is_null($p_languageId)) {
                $query .= " AND IdLanguage = '" . $g_ado_db->escape($p_languageId) . "'";
            }
            $query .= "\n";
            $queries[] = $query;
        }
        if (count($queries) == 0) {
            return null;
        }
        return implode("        union\n", $queries);
    }


    /**
     *
     */
    private static function ProcessListParameters($p_param, array &$p_otherTables = array())
    {
        $conditionOperation = array();

        $leftOperand = strtolower($p_param->getLeftOperand());
        $conditionOperation['left'] = $leftOperand;
        switch ($leftOperand) {
        case 'keyword':
            $conditionOperation['symbol'] = 'LIKE';
            $conditionOperation['right'] = '%'.$p_param->getRightOperand().'%';
            break;
        case 'onfrontpage':
            $conditionOperation['right'] = ($p_param->getRightOperand() == 1) ? 'Y' : 'N';
            break;
        case 'onsection':
            $conditionOperation['right'] = ($p_param->getRightOperand() == 1) ? 'Y' : 'N';
            break;
        case 'public':
            $conditionOperation['right'] = ($p_param->getRightOperand() == 1) ? 'Y' : 'N';
            break;
        case 'matchalltopics':
            $conditionOperation['symbol'] = '=';
            $conditionOperation['right'] = 'true';
            break;
        case 'topic':
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;
        case 'published':
            if (strtolower($p_param->getRightOperand()) == 'true') {
                $conditionOperation['symbol'] = '=';
                $conditionOperation['right'] =  'Y';
            }
            break;
        case 'workflow_status':
            $conditionOperation['symbol'] = '=';
            switch(strtolower($p_param->getRightOperand())) {
            case 'new':
                $conditionOperation['right'] = 'N';
                break;
            case 'published':
                $conditionOperation['right'] = 'Y';
                break;
            case 'submitted':
                $conditionOperation['right'] = 'S';
                break;
            case 'withissue':
                $conditionOperation['right'] = 'M';
                break;
            }
            break;
        case 'issue_published':
            $p_otherTables['Issues'] = array('IdPublication'=>'IdPublication',
            'NrIssue'=>'Number', 'IdLanguage'=>'IdLanguage');
            $conditionOperation['symbol'] = '=';
            $conditionOperation['right'] = 'Y';
            break;
        case 'reads':
            $p_otherTables['RequestObjects'] = array('object_id'=>'object_id');
        default:
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
     * Returns a select query for obtaining the articles that have the given topics
     *
     * @param array $p_TopicIds
     * @param array $p_typeAttributes
     * @param bool $p_negate
     * @return string
     */
    private static function BuildTopicSelectClause(array $p_TopicIds,
                                                   array $p_typeAttributes,
                                                   $p_negate = false)
    {
        $topicIds = array();
        foreach ($p_TopicIds as $topicId) {
            if (is_array($topicId)) {
                $topicIds = array_merge($topicIds, $topicId);
            } else {
                $topicIds[] = $topicId;
            }
        }
        $notCondition = $p_negate ? ' NOT' : '';
        if (!$p_negate) {
            $selectClause = '        SELECT NrArticle FROM ArticleTopics WHERE TopicId'
                          . ' IN (' . implode(', ', $topicIds) . ")\n";
        } else {
            $selectClause = "        SELECT a.Number\n"
                          . "        FROM Articles AS a LEFT JOIN ArticleTopics AS at\n"
                          . "          ON a.Number = at.NrArticle\n"
                          . "        WHERE TopicId IS NULL OR TopicId NOT IN ("
                          . implode(', ', $topicIds) . ")\n";
        }
        foreach ($p_typeAttributes as $typeAttribute) {
            $selectClause .= "        UNION\n"
                          . "        SELECT NrArticle FROM `X" . $typeAttribute->getArticleType() . '`'
                          . " WHERE " . $typeAttribute->getName()
                          . "$notCondition IN (" . implode(', ', $topicIds) . ")\n";
        }
        return $selectClause;
    }


    /**
     * Performs a search against the article content using the given
     * keywords. Returns the list of articles matching the given criteria.
     *
     * @param string $p_searchPhrase
     * @param string $p_fieldName - may be 'title' or 'author'
     * @param bool $p_matchAll - true if all keyword have to match
     * @param array $p_constraints
     * @param array $p_order
     * @param int $p_start - return results starting from the given order number
     * @param int $p_limit - return at most $p_limit rows
     * @param int $p_count - sets $p_count to the total number of rows in the search
     * @param bool $p_countOnly - if true returns only the total number of rows
     * @return array
     */
    public static function SearchByKeyword($p_searchPhrase,
                                           $p_matchAll = false,
                                           array $p_constraints = array(),
                                           array $p_order = array(),
                                           $p_start = 0,
                                           $p_limit = 0,
                                           &$p_count,
                                           $p_countOnly = false)
    {
        global $g_ado_db;

        $selectClauseObj = new SQLSelectClause();

        // set tables and joins between tables
        $selectClauseObj->setTable('Articles');

        if ($p_matchAll) {
            $p_searchPhrase = '__match_all ' . $p_searchPhrase;
        }
        $mainClauseConstraint = "(Articles.Number, Articles.IdLanguage) IN ("
        . ArticleIndex::SearchQuery($p_searchPhrase) . ")";
        $selectClauseObj->addWhere($mainClauseConstraint);

        $joinTables = array();
        // set other constraints
        foreach ($p_constraints as $constraint) {
            $leftOperand = $constraint->getLeftOperand();
            $operandAttributes = explode('.', $leftOperand);
            if (count($operandAttributes) == 2) {
                $table = trim($operandAttributes[0]);
                if (strtolower($table) != 'articles') {
                    $joinTables[] = $table;
                }
            }
            $symbol = $constraint->getOperator()->getSymbol('sql');
            $rightOperand = "'" . $g_ado_db->escape($constraint->getRightOperand()) . "'";
            $selectClauseObj->addWhere("$leftOperand $symbol $rightOperand");
        }
        foreach ($joinTables as $table) {
            $selectClauseObj->addJoin("LEFT JOIN $table ON Articles.Number = $table.NrArticle");
        }

        // create the count clause object
        $countClauseObj = clone $selectClauseObj;

        // set the columns for the select clause
        $selectClauseObj->addColumn('Articles.Number');
        $selectClauseObj->addColumn('Articles.IdLanguage');

        // set the order for the select clause
        $p_order = count($p_order) > 0 ? $p_order : Article::$s_defaultOrder;
        $order = Article::ProcessListOrder($p_order);
        foreach ($order as $orderDesc) {
            $orderField = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
        }

        // sets the LIMIT start and offset values
        $selectClauseObj->setLimit($p_start, $p_limit);

        // set the column for the count clause
        $countClauseObj->addColumn('COUNT(*)');

        $articlesList = array();
        if (!$p_countOnly) {
            $selectQuery = $selectClauseObj->buildQuery();
            $articles = $g_ado_db->GetAll($selectQuery);
            foreach ($articles as $article) {
                $articlesList[] = new Article($article['IdLanguage'], $article['Number']);
            }
        }
        $countQuery = $countClauseObj->buildQuery();
        $p_count = $g_ado_db->GetOne($countQuery);
        return $articlesList;
    }


    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bynumber, byname, bydate, bycreationdate, bypublishdate,
     *        bypublication, byissue, bysection, bylanguage, bysectionorder,
     *        bypopularity, bycomments
     *      order_direction can take one of the following values:
     *        asc, desc
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order, array &$p_otherTables = array(),
    &$p_whereConditions = array())
    {
        if (!is_array($p_whereConditions)) {
            $p_whereConditions = array();
        }
        $order = array();
        foreach ($p_order as $orderDesc) {
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
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
                    $dbField = 'Articles.PublishDate';
                    break;
                case 'bylastupdate':
                	$dbField = 'Articles.time_updated';
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
                case 'bypopularity':
                    $dbField = 'RequestObjects.request_count';
                    $p_otherTables['RequestObjects'] = array('object_id'=>'object_id');
                    break;
                case 'bycomments':
                    //@todo change this with DOCTRINE2 when refactor
		            $dbField = 'comments_counter.comments_count';
                    $joinTable = "(SELECT COUNT(*) AS comments_count, `fk_thread_id` AS `fk_article_number`, fk_language_id \n"
                               . "    FROM `Comment` `c` \n"
                               . "    WHERE c.status = 0 \n"
                               . '    GROUP BY fk_thread_id, fk_language_id)';
                    $p_otherTables[$joinTable] = array('__TABLE_ALIAS'=>'comments_counter',
                                                       'Number'=>'fk_article_number',
                                                       'IdLanguage'=>'fk_language_id');
                    break;
                case 'bylastcomment':
                    //@todo change this with DOCTRINE2 when refactor
        		    $dbField = 'comment_ids.last_comment_id';
                    $joinTable = "(SELECT MAX(id) AS last_comment_id, `fk_thread_id` AS `fk_article_number`, fk_language_id \n"
                               . "    FROM Comment c \n"
                               . "    WHERE c.status = 0 \n"
                               . "    GROUP BY fk_thread_id, fk_language_id)";
                    $p_otherTables[$joinTable] =  array('__TABLE_ALIAS'=>'comment_ids',
                                                        'Number'=>'fk_article_number',
                                                        'IdLanguage'=>'fk_language_id');
                    $p_whereConditions[] = "`comment_ids`.`last_comment_id` IS NOT NULL";
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
                $order[] = array('field'=>$dbField, 'dir'=>$direction);
            }
        }
        return $order;
    }


    /**
     * Performs a search against the given article field using the given
     * keywords. Returns the list of articles matching the given criteria.
     *
     * @param array $p_keywords
     * @param string $p_fieldName - may be 'title' or 'author'
     * @param bool $p_matchAll - true if all keyword have to match
     * @param array $p_constraints
     * @param array $p_order
     * @param int $p_start - return results starting from the given order number
     * @param int $p_limit - return at most $p_limit rows
     * @param int $p_count - sets $p_count to the total number of rows in the search
     * @param bool $p_countOnly - if true returns only the total number of rows
     * @return array
     */
    public static function SearchByField(array $p_keywords,
                                         $p_fieldName,
                                         $p_matchAll = false,
                                         array $p_constraints = array(),
                                         array $p_order = array(),
                                         $p_start = 0,
                                         $p_limit = 0,
                                         &$p_count,
                                         $p_countOnly = false)
    {
        global $g_ado_db;

        static $searchFields = array(
                'title'=>array('table_fields'=>array('Name'),
                               'table'=>'Articles'),
                'author'=>array('table_fields'=>array('first_name', 'last_name'),
                                'table'=>'ArticleAuthors',
                                'join_fields'=>array('Number'=>'fk_article_number')));

        $fieldName = strtolower($p_fieldName);
        if (!array_key_exists($fieldName, $searchFields)) {
            return false;
        }

        $selectClauseObj = new SQLSelectClause();

        // set tables and joins between tables
        $selectClauseObj->setTable('Articles');

        $joinTable = $searchFields[$fieldName]['table'];
        if ($joinTable != 'Articles') {
            $selectClauseObj->addTableFrom($joinTable);
            foreach ($searchFields[$fieldName]['join_fields'] as
                       $leftJoinField=>$rightJoinField) {
                $selectClauseObj->addWhere("`Articles`.`$leftJoinField` = "
                                           . "`$joinTable`.`$rightJoinField`");
            }
            if ($fieldName == 'author') {
                $joinTable = 'Authors';
                $selectClauseObj->addTableFrom($joinTable);
                $selectClauseObj->addWhere("`ArticleAuthors`.`fk_author_id` = "
                                           . "`$joinTable`.`id`");
            }
        }

        foreach ($searchFields[$fieldName]['table_fields'] as $matchField) {
            $matchFields[] = "`$joinTable`.`$matchField`";
        }
        $matchCond = 'MATCH (' . implode(', ', $matchFields) . ") AGAINST ('";
        foreach ($p_keywords as $keyword) {
            $matchCond .= ($p_matchAll ? '+' : '') . $g_ado_db->escape($keyword) . ' ';
        }
        $matchCond .= "' IN BOOLEAN MODE)";
        $selectClauseObj->addWhere($matchCond);

        $joinTables = array();
        // set other constraints
        foreach ($p_constraints as $constraint) {
            $leftOperand = $constraint->getLeftOperand();
            $operandAttributes = explode('.', $leftOperand);
            if (count($operandAttributes) == 2) {
                $table = trim($operandAttributes[0]);
                if (strtolower($table) != 'articles') {
                    $joinTables[] = $table;
                }
            }
            $symbol = $constraint->getOperator()->getSymbol('sql');
            $rightOperand = "'" . $g_ado_db->escape($constraint->getRightOperand()) . "'";
            $selectClauseObj->addWhere("$leftOperand $symbol $rightOperand");
        }
        foreach ($joinTables as $table) {
            $selectClauseObj->addJoin("LEFT JOIN $table ON Articles.Number = $table.NrArticle");
        }

        // create the count clause object
        $countClauseObj = clone $selectClauseObj;

        // set the columns for the select clause
        $selectClauseObj->addColumn('Articles.Number');
        $selectClauseObj->addColumn('Articles.IdLanguage');
        $selectClauseObj->addColumn($matchCond . ' AS score');

        // set the order for the select clause
        $p_order = count($p_order) > 0 ? $p_order : Article::$s_defaultOrder;
        $order = Article::ProcessListOrder($p_order);
        $selectClauseObj->addOrderBy('score DESC');
        foreach ($order as $orderDesc) {
            $orderField = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
        }

        // sets the LIMIT start and offset values
        $selectClauseObj->setLimit($p_start, $p_limit);

        // set the column for the count clause
        $countClauseObj->addColumn('COUNT(*)');

        $articlesList = array();
        if (!$p_countOnly) {
            $selectQuery = $selectClauseObj->buildQuery();
            $articles = $g_ado_db->GetAll($selectQuery);
            foreach ($articles as $article) {
                $articlesList[] = new Article($article['IdLanguage'], $article['Number']);
            }
        }
        $countQuery = $countClauseObj->buildQuery();
        $p_count = $g_ado_db->GetOne($countQuery);
        return $articlesList;
    }



    /**
     * Processes an order directive for the article translations list.
     *
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bynumber, byname, byenglish_name, bycode
     *      order_direction can take one of the following values:
     *        asc, desc
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessLanguageListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            $dbField = null;
            switch (strtolower($field)) {
                case 'bynumber':
                    $dbField = 'Languages.Id';
                    break;
                case 'byname':
                    $dbField = 'Languages.OrigName';
                    break;
                case 'byenglish_name':
                    $dbField = 'Languages.Name';
                    break;
                case 'bycode':
                    $dbField = 'Languages.Code';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[] = array('field'=>$dbField, 'dir'=>$direction);
        }
        return $order;
    }
} // class Article

?>
