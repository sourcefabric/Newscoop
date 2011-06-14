<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');

/**
 * @package Campsite
 */
class Section extends DatabaseObject {
	var $m_dbTableName = 'Sections';
	var $m_keyColumnNames = array(
		'IdPublication',
		'NrIssue',
		'IdLanguage',
		'Number');
	var $m_columnNames = array(
		'id',
		'IdPublication',
		'NrIssue',
		'IdLanguage',
		'Number',
		'Name',
		'ShortName',
		'Description',
		'SectionTplId',
		'ArticleTplId');
	var $m_languageName = null;

	/**
	 * A section is a part of an issue.
	 * @param int $p_publicationId
	 * @param int $p_issueNumber
	 * @param int $p_languageId
	 * @param int $p_sectionNumber
	 */
	public function Section($p_publicationId = null, $p_issueNumber = null,
	                        $p_languageId = null, $p_sectionNumber = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['NrIssue'] = $p_issueNumber;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_sectionNumber;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // fn Section


	/**
	 * Create a new Section.
	 * @param string $p_name
	 * @param string $p_shortName
	 */
	public function create($p_name, $p_shortName, $p_columns = null)
	{
		if (!is_array($p_columns)) {
			$p_columns = array();
		}
		$p_columns['Name'] = $p_name;
		$p_columns['ShortName'] = $p_shortName;
		$success = parent::create($p_columns);
		if ($success) {
			global $g_ado_db;
			$sql = "UPDATE `Sections` s".
			" JOIN `Issues` AS i ON i.`IdPublication` = s.`IdPublication` AND i.`Number` = s.`NrIssue` AND i.`IdLanguage` = s.`IdLanguage`".
			" SET `fk_issue_id` = i.`id` WHERE ".
			" s.`IdPublication` = ".$this->m_data['IdPublication'].
			" AND s.`NrIssue` = ".$this->m_data['NrIssue'].
			" AND s.`Number` = ".$this->m_data['Number'].
			" AND s.`IdLanguage` = ".$this->m_data['IdLanguage'];
			$g_ado_db->Execute($sql);
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Section "$1" ($2) added. (Publication: $3, Issue: $4)',
					 $this->m_data['Name'],
					 $this->m_data['Number'],
					 $this->m_data['IdPublication'],
					 $this->m_data['NrIssue']);
			Log::Message($logtext, null, 21);
		}
		return $success;
	} // fn create


	/**
	 * Copy the section to the given issue.  The issue can be the same as
	 * the current issue.  All articles will be copied to the new section.
	 *
	 * @param int $p_destPublicationId
	 *     The destination publication ID.
	 * @param int $p_destIssueNumber
	 *     The destination issue ID.
	 * @param int $p_destIssueLanguageId
	 *     (optional) The destination issue language ID.  If not given,
	 *     it will use the language ID of this section.
	 * @param int $p_destSectionNumber
	 *     (optional) The destination section ID.  If not given, a new
	 *     section will be created.
	 * @param boolean $p_copyArticles
	 *     (optional) If set to true, all articles will be copied to the
	 *     destination section.
	 * @return Section
	 *     The new Section object.
	 */
	public function copy($p_destPublicationId, $p_destIssueNumber,
				         $p_destIssueLanguageId = null,$p_destSectionNumber = null,
				         $p_copyArticles = true)
	{
		if (is_null($p_destIssueLanguageId)) {
			$p_destIssueLanguageId = $this->m_data['IdLanguage'];
		}
		if (is_null($p_destSectionNumber)) {
			$p_destSectionNumber = $this->m_data['Number'];
		}
		$dstSectionObj = new Section($p_destPublicationId,
					$p_destIssueNumber,
					$p_destIssueLanguageId,
					$p_destSectionNumber);
		// If source issue and destination issue are the same
		if ( ($this->m_data['IdPublication'] == $p_destPublicationId)
			&& ($this->m_data['NrIssue'] == $p_destIssueNumber)
			&& ($this->m_data['IdLanguage'] == $p_destIssueLanguageId) ) {
			$shortName = $p_destSectionNumber;
			$sectionName = $this->getName() . " (duplicate)";
		} else {
			$shortName = $this->getUrlName();
			$sectionName = $this->getName();
		}
		$dstSectionCols = array();
		$dstSectionCols['SectionTplId'] = $this->m_data['SectionTplId'];
		$dstSectionCols['ArticleTplId'] = $this->m_data['ArticleTplId'];

		// Create the section if it doesnt exist yet.
		if (!$dstSectionObj->exists()) {
			$dstSectionObj->create($sectionName, $shortName, $dstSectionCols);
		}
		
		// Copy all the articles.
		if ($p_copyArticles) {
			$srcSectionArticles = Article::GetArticles($this->m_data['IdPublication'],
							$this->m_data['NrIssue'],
							$this->m_data['Number']);
			$copiedArticles = array();
			foreach ($srcSectionArticles as $articleObj) {
				if (!in_array($articleObj->getArticleNumber(), $copiedArticles)) {
					$tmpCopiedArticles = $articleObj->copy($p_destPublicationId,
					$p_destIssueNumber, $p_destSectionNumber, null, true);
					$copiedArticles[] = $articleObj->getArticleNumber();
				}
			}
		}

		return $dstSectionObj;
	} // fn copy


	/**
	 * Delete the section, and optionally the articles.  If you are
	 * deleting the articles, the default is to only delete the articles
	 * with the same language as this section.  If you want to delete
	 * all article translations, set the second parameter to TRUE.
	 *
	 * @param boolean $p_deleteArticles
	 * @param boolean $p_deleteArticleTranslations
	 * @return int
	 * 		Return the number of articles deleted.
	 */
	public function delete($p_deleteArticles = false, $p_deleteArticleTranslations = false)
	{
		$numArticlesDeleted = 0;
		if ($p_deleteArticles) {
			$languageId = null;
			if (!$p_deleteArticleTranslations) {
				$languageId = $this->m_data['IdLanguage'];
			}
			$articles = Article::GetArticles($this->m_data['IdPublication'],
						$this->m_data['NrIssue'],
						$this->m_data['Number'],
						$languageId);
			$numArticlesDeleted = count($articles);
			foreach ($articles as $deleteMe) {
				$deleteMe->delete();
			}
		}
		$tmpData = $this->m_data;
		$success = parent::delete();
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Section "$1" ($2) deleted. (Publication: $3, Issue: $4)',
					 $tmpData['Name'], $tmpData['Number'],
					 $tmpData['IdPublication'], $tmpData['NrIssue']);
			Log::Message($logtext, null, 22);
			$outputSettingSections = $this->getOutputSettingSectionService()->findBySection($tmpData['id']);
			foreach($outputSettingSections as $outputSet){
				$this->getOutputSettingSectionService()->delete($outputSet);
			}
		}
		return $numArticlesDeleted;
	} // fn delete

	/**
	 * Return the section ID.
	 * @return int
	 */
	public function getSectionId()
	{
		return $this->m_data['id'];
	} // fn getId
	
	/**
	 * @return int
	 */
	public function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * @return int
	 */
	public function getIssueNumber()
	{
		return $this->m_data['NrIssue'];
	} // fn getIssueNumber


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->m_data['IdLanguage'];
	} // fn getLanguageId


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
	 * @return int
	 */
	public function getSectionNumber()
	{
		return $this->m_data['Number'];
	} // fn getSectionNumber


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * @param string $p_value
	 * @return boolean
	 */
	public function setName($p_value)
	{
		return $this->setProperty('Name', $p_value);
	} // fn setName


	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->m_data['Description'];
	} // fn getDescription


	/**
	 * @param string $p_value
	 * @return boolean
	 */
	public function setDescription($p_value)
	{
		return $this->setProperty('Description', $p_value);
	} // fn setDescription


	/**
	 * @return string
	 */
	public function getUrlName()
	{
		return $this->m_data['ShortName'];
	} // fn getUrlName


	/**
	 * @param string $p_name
	 */
	public function setUrlName($p_name)
	{
		return $this->setProperty('ShortName', $p_name);
	} // fn setUrlName


	/**
	 * @return int
	 */
	public function getArticleTemplateId()
	{
		return $this->m_data['ArticleTplId'];
	} // fn getArticleTemplateId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	public function setArticleTemplateId($p_value)
	{
		return $this->setProperty('ArticleTplId', $p_value);
	} // fn setArticleTemplateId


	/**
	 * @return int
	 */
	public function getSectionTemplateId()
	{
		return $this->m_data['SectionTplId'];
	} // fn getSectionTemplateId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	public function setSectionTemplateId($p_value)
	{
		return $this->setProperty('SectionTplId', $p_value);
	} // fn setSectionTemplateId
    
	/**
	 * Return an array of sections in the given issue.
	 * @param int $p_publicationId
	 * 		(Optional) Only return sections in this publication.
	 *
	 * @param int $p_issueNumber
	 *		(Optional) Only return sections in this issue.
	 *
	 * @param int $p_languageId
	 * 		(Optional) Only return sections that have this language ID.
	 *
	 * @param string $p_urlName
	 * 		(Optional) Only return sections that have this URL name.
	 *
	 * @param string $p_sectionName
	 * 		(Optional) Only return sections that have this name.
	 *
	 * @param array $p_sqlOptions
	 *		(Optional) Additional options.  See DatabaseObject::ProcessOptions().
	 *
	 * @return array
	 */
	public static function GetSections($p_publicationId = null, $p_issueNumber = null,
	                                   $p_languageId = null, $p_urlName = null,
	                                   $p_sectionName = null, $p_sqlOptions = null, $p_skipCache = false)
	{
	    if (!$p_skipCache && CampCache::IsEnabled()) {
	    	$paramsArray['publication_id'] = (is_null($p_publicationId)) ? 'null' : $p_publicationId;
	    	$paramsArray['issue_number'] = (is_null($p_issueNumber)) ? 'null' : $p_issueNumber;
	    	$paramsArray['language_id'] = (is_null($p_languageId)) ? 'null' : $p_languageId;
	    	$paramsArray['url_name'] = (is_null($p_urlName)) ? 'null' : $p_urlName;
	    	$paramsArray['section_name'] = (is_null($p_sectionName)) ? 'null' : $p_sectionName;
	    	$paramsArray['sql_options'] = (is_null($p_sqlOptions)) ? 'null' : $p_sqlOptions;
	    	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
	    	$sectionsList = $cacheListObj->fetchFromCache();
	    	if ($sectionsList !== false && is_array($sectionsList)) {
	    		return $sectionsList;
	    	}
	    }

	    $constraints = array();
	    if (!is_null($p_publicationId)) {
	        $constraints[] = array("IdPublication", $p_publicationId);
	    }
	    if (!is_null($p_issueNumber)) {
	        $constraints[] = array("NrIssue", $p_issueNumber);
	    }
	    if (!is_null($p_languageId)) {
	        $constraints[] = array("IdLanguage", $p_languageId);
	    }
	    if (!is_null($p_urlName)) {
	        $constraints[] = array("ShortName", $p_urlName);
	    }
	    if (!is_null($p_sectionName)) {
	        $constraints[] = array("Name", $p_sectionName);
	    }
	    $sectionsList = DatabaseObject::Search('Section', $constraints, $p_sqlOptions);
	    if (!$p_skipCache && CampCache::IsEnabled()) {
	        $cacheListObj->storeInCache($sectionsList);
	    }

	    return $sectionsList;
	} // fn GetSections


	/**
	 * Return an array of arrays indexed by "id" and "name".
	 * @return array
	 */
	public static function GetUniqueSections($p_publicationId, $p_byLanguage = false)
	{
		global $g_ado_db;
		$queryStr = "SELECT Number as id, s.Name as name, s.IdLanguage, l.Name as LangName "
					." FROM Sections AS s LEFT JOIN Languages AS l ON s.IdLanguage = l.Id"
					." WHERE s.IdPublication = $p_publicationId"
					." GROUP BY s.Number";
		if ($p_byLanguage) {
			$queryStr .= ", s.IdLanguage";
		}
		return $g_ado_db->GetAll($queryStr);
	} // fn GetSectionNames


	/**
	 * Return the total number of sections according to the given values.
	 * @param int $p_publicationId
	 * @param int $p_issueNumber
	 * @param int $p_languageId
	 * @return int
	 */
	public static function GetTotalSections($p_publicationId = null,
	                                        $p_issueNumber = null,
	                                        $p_languageId = null)
	{
		global $g_ado_db;
		$queryStr = 'SELECT COUNT(*) FROM Sections';
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_issueNumber)) {
			$whereClause[] = "NrIssue=$p_issueNumber";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetTotalSections


	public static function GetNumUniqueSections($p_publicationId, $p_byLanguage = true)
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM Sections WHERE IdPublication = $p_publicationId"
				." GROUP BY Number";
		if ($p_byLanguage) {
			$queryStr .= ', IdLanguage';
		}
		$result = $g_ado_db->Execute($queryStr);
		return $result->RowCount();
	}


	/**
	 * Return a section number that is not in use.
	 * @param int $p_publicationId
	 * @param int $p_issueNumber
	 * @param int $p_languageId
	 * @return int
	 */
	public static function GetUnusedSectionNumber($p_publicationId, $p_issueNumber)
	{
		global $g_ado_db;
		$queryStr = "SELECT MAX(Number) + 1 FROM Sections "
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueNumber";
		$number = 0 + $g_ado_db->GetOne($queryStr);
		if ($number <= 0) {
			$number++;
		}
		return $number;
	} // fn GetUnusedSectionNumber


    /**
     * Returns a sections list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     *
     * @return array $sectionsList
     *    An array of Section objects
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
        	$sectionsList = $cacheListObj->fetchFromCache();
        	if ($sectionsList !== false && is_array($sectionsList)) {
        		return $sectionsList;
        	}
        }

        $hasPublicationId = false;
        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (empty($comparisonOperation)) {
                break;
            }
            if (strpos($comparisonOperation['left'], 'IdPublication') !== false) {
                $hasPublicationId = true;
            }

            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $g_ado_db->escape($comparisonOperation['right']) . "' ";
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // validates whether publication identifier was given
        if ($hasPublicationId == false) {
            CampTemplate::singleton()->trigger_error('missed parameter Publication '
                .'Identifier in statement list_sections');
            return;
        }

        // sets the columns to be fetched
        $tmpSection = new Section();
		$columnNames = $tmpSection->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }
        $countClauseObj->addColumn('COUNT(*)');

        // sets the main table for the query
        $selectClauseObj->setTable($tmpSection->getDbTableName());
        $countClauseObj->setTable($tmpSection->getDbTableName());
        unset($tmpSection);

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the order condition if any
        foreach ($p_order as $orderColumn => $orderDirection) {
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        $selectClauseObj->addGroupField('Number');
        $selectClauseObj->addGroupField('IdLanguage');

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $countQuery = $countClauseObj->buildQuery();
        $sections = $g_ado_db->GetAll($selectQuery);
        if (is_array($sections)) {
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of section objects
        	$sectionsList = array();
        	foreach ($sections as $section) {
        		$secObj = new Section($section['IdPublication'],
        		$section['NrIssue'],
        		$section['IdLanguage'],
        		$section['Number']);
        		if ($secObj->exists()) {
        			$sectionsList[] = $secObj;
        		}
        	}
        } else {
        	$sectionsList = array();
        	$p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($sectionsList);
        }

        return $sectionsList;
    } // fn GetList


    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $comparisonOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'name':
            $comparisonOperation['left'] = 'Name';
            break;
        case 'number':
            $comparisonOperation['left'] = 'Number';
            break;
        case 'idpublication':
            $comparisonOperation['left'] = 'IdPublication';
            break;
        case 'nrissue':
            $comparisonOperation['left'] = 'NrIssue';
            break;
        case 'idlanguage':
            $comparisonOperation['left'] = 'IdLanguage';
            break;
        }

        if (isset($comparisonOperation['left'])) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
            $comparisonOperation['right'] = $p_param->getRightOperand();
        }

        return $comparisonOperation;
    } // fn ProcessListParameters

    public static function BuildSectionIdsQuery(array $p_shortNames, int $p_publication = NULL) {
        $sections_query = false;
        $section_names = array();

        foreach ($p_shortNames as $one_name) {
            $one_name = str_replace('"', '""', trim($one_name));
            if (0 < strlen($one_name)) {
                $section_names[] = $one_name;
            }
        }

        $pub_cons = "";
        if ($p_publication && (is_numeric($p_publication))) {$pub_cons .= " AND IdPublication = $p_publication";}

        if (0 < count($section_names)) {
            $names_str = '"' . implode('", "', $section_names) . '"';
            $sections_query = "SELECT Number AS id FROM Sections WHERE trim(ShortName) IN ($names_str)" . $pub_cons;

        }

        return $sections_query;
    } // fn BuildSectionIdsQuery

} // class Section
?>
