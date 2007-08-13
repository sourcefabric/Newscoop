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
require_once($g_documentRoot.'/classes/SQLSelectClause.php');
require_once($g_documentRoot.'/classes/Log.php');

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
	function Section($p_publicationId = null, $p_issueNumber = null,
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
	function create($p_name, $p_shortName, $p_columns = null)
	{
		if (!is_array($p_columns)) {
			$p_columns = array();
		}
		$p_columns['Name'] = $p_name;
		$p_columns['ShortName'] = $p_shortName;
		$success = parent::create($p_columns);
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Section $1 added. (Issue: $2, Publication: $3)',
			$this->m_data['Name']." (".$this->m_data['Number'].")",
			$this->m_data['NrIssue'],
			$this->m_data['IdPublication']);
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
	function copy($p_destPublicationId, $p_destIssueNumber,
				  $p_destIssueLanguageId = null,$p_destSectionNumber = null,
				  $p_copyArticles = true)
	{
		if (is_null($p_destIssueLanguageId)) {
			$p_destIssueLanguageId = $this->m_data['IdLanguage'];
		}
		if (is_null($p_destSectionNumber)) {
			$p_destSectionNumber = $this->m_data['Number'];
		}
		$dstSectionObj =& new Section($p_destPublicationId,
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
	function delete($p_deleteArticles = false, $p_deleteArticleTranslations = false)
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
		$success = parent::delete();
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Section $1 deleted. (Issue: $2, Publication: $3)',
			$this->m_data['Name']." (".$this->m_data['Number'].")",
			$this->m_data['NrIssue'],
			$this->m_data['IdPublication']);
			Log::Message($logtext, null, 22);
		}
		return $numArticlesDeleted;
	} // fn delete


	/**
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * @return int
	 */
	function getIssueNumber()
	{
		return $this->m_data['NrIssue'];
	} // fn getIssueNumber


	/**
	 * @return int
	 */
	function getLanguageId()
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
	function getLanguageName()
	{
		if (is_null($this->m_languageName)) {
			$language =& new Language($this->m_data['IdLanguage']);
			$this->m_languageName = $language->getNativeName();
		}
		return $this->m_languageName;
	} // fn getLanguageName


	/**
	 * @return int
	 */
	function getSectionNumber()
	{
		return $this->m_data['Number'];
	} // fn getSectionNumber


	/**
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * @param string $p_value
	 * @return boolean
	 */
	function setName($p_value)
	{
		return $this->setProperty('Name', $p_value);
	} // fn setName


	/**
	 * @return string
	 */
	function getDescription()
	{
		return $this->m_data['Description'];
	} // fn getDescription


	/**
	 * @param string $p_value
	 * @return boolean
	 */
	function setDescription($p_value)
	{
		return $this->setProperty('Description', $p_value);
	} // fn setDescription


	/**
	 * @return string
	 */
	function getUrlName()
	{
		return $this->m_data['ShortName'];
	} // fn getUrlName


	/**
	 * @param string $p_name
	 */
	function setUrlName($p_name)
	{
		return $this->setProperty('ShortName', $p_name);
	} // fn setUrlName


	/**
	 * @return int
	 */
	function getArticleTemplateId()
	{
		return $this->m_data['ArticleTplId'];
	} // fn getArticleTemplateId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setArticleTemplateId($p_value)
	{
		return $this->setProperty('ArticleTplId', $p_value);
	} // fn setArticleTemplateId


	/**
	 * @return int
	 */
	function getSectionTemplateId()
	{
		return $this->m_data['SectionTplId'];
	} // fn getSectionTemplateId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setSectionTemplateId($p_value)
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
	function GetSections($p_publicationId = null, $p_issueNumber = null,
	                     $p_languageId = null, $p_urlName = null,
	                     $p_sectionName = null, $p_sqlOptions = null)
	{
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

		return DatabaseObject::Search('Section', $constraints, $p_sqlOptions);
	} // fn GetSections


	/**
	 * Return an array of arrays indexed by "id" and "name".
	 * @return array
	 */
	function GetUniqueSections($p_publicationId, $p_byLanguage = false)
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
	function GetTotalSections($p_publicationId = null, $p_issueNumber = null, $p_languageId = null)
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


	function GetNumUniqueSections($p_publicationId, $p_byLanguage = true)
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
	function GetUnusedSectionNumber($p_publicationId, $p_issueNumber, $p_languageId)
	{
		global $g_ado_db;
		$queryStr = "SELECT MAX(Number) + 1 FROM Sections "
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueNumber AND IdLanguage=$p_languageId";
		$number = 0 + $g_ado_db->GetOne($queryStr);
		if ($number <= 0) {
			$number++;
		}
		return $number;
	} // fn GetUnusedSectionNumber


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

        $tmpSection =& new Section();
		$columnNames = $tmpSection->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $sqlClauseObj->addColumn($columnName);
        }

        $sqlClauseObj->setTable($tmpSection->getDbTableName());
        unset($tmpSection);

        foreach ($p_parameters as $condition) {
            switch (strtolower($condition->getLeftOperand())) {
            case 'name':
                $leftOperand = 'Name';
                $rightOperand = (string)$condition->getRightOperand();
                break;
            case 'number':
                $leftOperand = 'Number';
                $rightOperand = (int)$condition->getRightOperand();
                break;
            }

            $operatorObj = $condition->getOperator();
            $whereCondition = $leftOperand . ' '
                . $operatorObj->getSymbol('sql') . " '"
                . $rightOperand . "' ";
            $sqlClauseObj->addWhere($whereCondition);
        }

        if (!is_array($p_order)) {
            $p_order = array();
        }

        foreach ($p_order as $orderColumn => $orderDirection) {
            $sqlClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        $sqlClauseObj->setLimit($p_start, $p_limit);

        $sqlQuery = $sqlClauseObj->buildQuery();
        var_dump($sqlQuery); echo '<br /><br />';
        $sections = $g_ado_db->Execute($sqlQuery);
        if (!$sections) {
            return null;
        }

        $sectionsList = array();
        foreach ($sections as $section) {
            $sectionsList[] = new Section($section['IdPublication'],
                                          $section['NrIssue'],
                                          $section['IdLanguage'],
                                          $section['Number']);
        }

        return $sectionsList;
    } // fn GetList

} // class Section
?>
