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
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Section.php');

/**
 * @package Campsite
 */
class Issue extends DatabaseObject {
	var $m_dbTableName = 'Issues';
	var $m_keyColumnNames = array('IdPublication', 'Number', 'IdLanguage');
	var $m_columnNames = array(
		'IdPublication',
		'Number',
		'IdLanguage',
		'Name',
		'PublicationDate',
		'Published',
		'IssueTplId',
		'SectionTplId',
		'ArticleTplId',
		'ShortName');
	var $m_languageName = null;

	/**
	 * A publication has Issues, Issues have Sections and Articles.
	 * @param int $p_publicationId
	 * @param int $p_languageId
	 * @param int $p_issueNumber
	 */
	function Issue($p_publicationId = null, $p_languageId = null, $p_issueNumber = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_issueNumber;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Create an issue.
	 * @param string $p_shortName
	 * @param array $p_values
	 * @return boolean
	 */
	function create($p_shortName, $p_values = null)
	{
	    $tmpValues = array('ShortName' => $p_shortName);
	    if (!is_null($p_values)) {
	       $tmpValues = array_merge($p_values, $tmpValues);
	    }
	    $success = parent::create($tmpValues);
	    if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
	    	$logtext = getGS('Issue $1 added in publication $2',
	    					 $this->m_data['Name']." (".$this->m_data['Number'].")",
	    					 $this->m_data['IdPublication']);
    		Log::Message($logtext, null, 11);
	    }
	    return $success;
	} // fn create


	/**
	 * Delete the Issue, and optionally all sections and articles contained within it.
	 * @param boolean $p_deleteSections
	 * @param boolean $p_deleteArticles
	 * @return boolean
	 */
	function delete($p_deleteSections = true, $p_deleteArticles = true)
	{
		global $g_ado_db;
		if ($p_deleteSections) {
    		$sections = Section::GetSections($this->m_data['IdPublication'], $this->m_data['Number'], $this->m_data['IdLanguage']);
    		foreach ($sections as $section) {
    		    $section->delete($p_deleteArticles);
    		}
		}
	    $success = parent::delete();
	    if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
	    	$logtext = getGS('Issue $1 from publication $2 deleted',
	    		$this->m_data['Name']." (".$this->m_data['Number'].")",
	    		$this->m_data['IdPublication']);
			Log::Message($logtext, null, 12);
	    }
		return $success;
	} // fn delete


	/**
	 * Copy this issue and all sections.
	 * @param int $p_destPublicationId
	 * @param int $p_destIssueId
	 * @param int $p_destLanguageId
	 * @return Issue
	 */
	function __copy($p_destPublicationId, $p_destIssueId, $p_destLanguageId)
	{
        // Copy the issue
        $newIssue =& new Issue($p_destPublicationId, $p_destLanguageId, $p_destIssueId);
        $columns = array();
        $columns['Name'] = mysql_real_escape_string($this->getName());
    	$columns['IssueTplId'] = $this->m_data['IssueTplId'];
    	$columns['SectionTplId'] = $this->m_data['SectionTplId'];
    	$columns['ArticleTplId'] = $this->m_data['ArticleTplId'];
        $created = $newIssue->create($p_destIssueId, $columns);
        if ($created) {
        	// Copy the sections in the issue
            $sections = Section::GetSections($this->m_data['IdPublication'],
                $this->m_data['Number'], $this->m_data['IdLanguage']);
            foreach ($sections as $section) {
                $section->copy($p_destPublicationId, $p_destIssueId, $p_destLanguageId, null, false);
            }
            return $newIssue;
        } else {
            return null;
        }
	} // fn __copy


	/**
	 * Create a copy of this issue.  You can use this to:
	 * 1) Translate an issue.  In this case do:
	 *    $issue->copy(null, $issue->getIssueNumber(), $destinationLanguage);
	 * 2) Duplicate all translations of an issue within a publication:
	 *    $issue->copy();
	 * 3) Copy an issue to another publication:
	 *    $issue->copy($destinationPublication);
	 * Note: All sections will be copied, but not the articles.
	 *
	 * @param int $p_destPublicationId
	 *     (optional) Specify the destination publication.
	 *     Default is this issue's publication ID.
	 * @param int $p_destIssueId
	 *     (optional) Specify the destination issue ID.
	 *     If not specified, a new one will be generated.
	 * @param int $p_destLanguageId
	 *     (optional) Use this if you want the copy to be a translation of the current issue.
	 *     Default is to copy all translations of this issue.
	 * @return mixed
	 *		An array of Issues, a single Issue, or null.
	 */
	function copy($p_destPublicationId = null, $p_destIssueId = null, $p_destLanguageId = null)
	{
	    global $g_ado_db;
	    if (is_null($p_destPublicationId)) {
	        $p_destPublicationId = $this->m_data['IdPublication'];
	    }
	    if (is_null($p_destIssueId)) {
	        $p_destIssueId = Issue::GetUnusedIssueId($this->m_data['IdPublication']);
	    }
	    if (is_null($p_destLanguageId)) {
            $queryStr = 'SELECT * FROM Issues '
                        .' WHERE IdPublication='.$this->m_data['IdPublication']
                        .' AND Number='.$this->m_data['Number'];
            $srcIssues = DbObjectArray::Create('Issue', $queryStr);

            // Copy all translations of this issue.
            $newIssues = array();
            foreach ($srcIssues as $issue) {
                $newIssues[] = $issue->__copy($p_destPublicationId, $p_destIssueId, $issue->getLanguageId());
            }
            return $newIssues;
	    } else {
	        // Translate the issue.
	        return $this->__copy($p_destPublicationId, $p_destIssueId, $p_destLanguageId);
	    }
	} // fn copy


	/**
	 * Return the publication ID of the publication that contains this issue.
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->getProperty('IdPublication');
	} // fn getPublicationId


	/**
	 * Return the language ID of the issue.
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId


	/**
	 * Changing an issue's language will change the section language as well.
	 *
	 * @param int $p_value
	 */
	function setLanguageId($p_value)
	{
		global $g_ado_db;
		$sql = "UPDATE Sections SET IdLanguage=$p_value"
			  ." WHERE IdPublication=".$this->m_data['IdPublication']
			  ." AND NrIssue=".$this->m_data['Number']
			  ." AND IdLanguage=".$this->m_data['IdLanguage'];
		$success = $g_ado_db->Execute($sql);
		if ($success) {
			$this->setProperty('IdLanguage', $p_value);
		}
	} // fn setLanguageId


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
	function getIssueNumber()
	{
		return $this->getProperty('Number');
	} // fn getIssueNumber


	/**
	 * Get the name of the issue.
	 * @return string
	 */
	function getName()
	{
		return $this->getProperty('Name');
	} // fn getName


	/**
	 * Set the name of the issue.
	 * @param string
	 * @return boolean
	 */
	function setName($p_value)
	{
	    return $this->setProperty('Name', $p_value);
	} // fn setName


	/**
	 * Get the string used for the URL to this issue.
	 * @return string
	 */
	function getUrlName()
	{
		return $this->getProperty('ShortName');
	} // fn getUrlName


	/**
	 * Set the string used in the URL to access this Issue.
	 *
	 * @return boolean
	 */
	function setUrlName($p_value)
	{
	    return $this->setProperty('ShortName', $p_value);
	} // fn setUrlName


	/**
	 * Get the default template ID used for articles in this issue.
	 * @return int
	 */
	function getArticleTemplateId()
	{
		return $this->getProperty('ArticleTplId');
	} // fn getArticleTemplateId


	/**
	 * Get the default template ID used for sections in this issue.
	 * @return int
	 */
	function getSectionTemplateId()
	{
		return $this->getProperty('SectionTplId');
	} // fn getSectionTemplateId


	/**
	 * Get the template ID used for this issue.
	 * @return int
	 */
	function getIssueTemplateId()
	{
		return $this->getProperty('IssueTplId');
	} // fn getIssueTemplateId


	/**
	 * Return 'Y' if the issue is published, 'N' if it is not published.
	 * @return string
	 */
	function getPublished()
	{
		return $this->getProperty('Published');
	} // fn getPublished


	/**
	 * Set the published state of the issue.
	 *
	 * @param string $p_value
	 *		Can be NULL, 'Y', 'N', TRUE, or FALSE.
	 *		If set to NULL, the current value will be reversed.
	 *
	 * @return void
	 */
	function setPublished($p_value = null)
	{
		$doPublish = null;
		if (is_null($p_value)) {
			if ($this->m_data['Published'] == 'Y') {
				$doPublish = false;
			} else {
				$doPublish = true;
			}
		} else {
			if (is_string($p_value)) {
				$p_value = strtoupper($p_value);
			}
			if (($this->m_data['Published'] == 'N') && (($p_value == 'Y') || ($p_value === true))) {
				$doPublish = true;
			} elseif (($this->m_data['Published'] == 'Y') && (($p_value == 'N') || ($p_value === false))) {
				$doPublish = false;
			}
		}
		if (!is_null($doPublish)) {
			if ($doPublish) {
				$this->setProperty('Published', 'Y', true);
				$this->setProperty('PublicationDate', 'NOW()', true, true);
			} else {
				$this->setProperty('Published', 'N', true);
			}

			// Log message
			if ($this->getPublished() == 'Y') {
				$status = getGS('Published');
			} else {
				$status = getGS('Not published');
			}
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Issue $1 changed status to $2',
							 $this->m_data['Number'].'. '.$this->m_data['Name'].' ('.$this->getLanguageName().')',
							 $status);
			Log::Message($logtext, null, 14);
		}
	} // fn setPublished


	function getPublicationDate()
	{
		return $this->getProperty('PublicationDate');
	} // fn getPublicationDate


	/**
	 * Get all the languages to which this issue has not been translated.
	 * @return array
	 */
	function getUnusedLanguages()
	{
		$tmpLanguage =& new Language();
		$columnNames = $tmpLanguage->getColumnNames(true);
		$queryStr = "SELECT ".implode(',', $columnNames)
					." FROM Languages LEFT JOIN Issues "
					." ON Issues.IdPublication = ".$this->m_data['IdPublication']
					." AND Issues.Number= ".$this->m_data['Number']
					." AND Issues.IdLanguage = Languages.Id "
					." WHERE Issues.IdPublication IS NULL";
		$languages = DbObjectArray::Create('Language', $queryStr);
		return $languages;
	} // fn getUsusedLanguages


	/**
	 * Get all the issues in the given publication as return them as an array
	 * of Issue objects.
	 *
	 * @param int $p_publicationId
	 * 		The publication ID.
	 *
	 * @param int $p_languageId
	 *		(Optional) Only return issues with this language.
	 *
	 * @param int $p_issueId
	 *		(Optional) Only return issues with this Issue ID.
	 *
	 * @param int $p_preferredLanguage
	 *		(Optional) List this language before others.  This will override any 'ORDER BY' sql
	 *		options you have.
	 *
	 * @param array $p_sqlOptions
	 *
	 * @return array
	 */
	function GetIssues($p_publicationId = null,
	                   $p_languageId = null,
	                   $p_issueId = null,
	                   $p_preferredLanguage = null,
	                   $p_sqlOptions = null)
	{
		$tmpIssue =& new Issue();
		$columnNames = $tmpIssue->getColumnNames(true);
		$queryStr = 'SELECT '.implode(',', $columnNames);
		if (!is_null($p_preferredLanguage)) {
			$queryStr .= ", abs(IdLanguage-$p_preferredLanguage) as LanguageOrder";
			$p_sqlOptions['ORDER BY'] = array('Number' => 'DESC', 'LanguageOrder' => 'ASC');
		}
		// We have to display the language name so oftern that we might
		// as well fetch it by default.
		$queryStr .= ', Languages.OrigName as LanguageName';
		$queryStr .= ' FROM Issues, Languages ';
		$whereClause = array();
		$whereClause[] = "Issues.IdLanguage=Languages.Id";
		if (!is_null($p_publicationId)) {
			$whereClause[] = "Issues.IdPublication=$p_publicationId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "Issues.IdLanguage=$p_languageId";
		}
		if (!is_null($p_issueId)) {
			$whereClause[] = "Issues.Number=$p_issueId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		global $g_ado_db;
		$issues = array();
		$rows = $g_ado_db->GetAll($queryStr);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpObj =& new Issue();
				$tmpObj->fetch($row);
				$tmpObj->m_languageName = $row['LanguageName'];
				$issues[] = $tmpObj;
			}
		}

		return $issues;
	} // fn GetIssues

	/**
	 * Return the total number of issues in the database.
	 *
	 * @param int $p_publicationId
	 *		If specified, return the total number of issues in the given publication.
	 *
	 * @return int
	 */
	function GetNumIssues($p_publicationId = null)
	{
		global $g_ado_db;
		$queryStr = 'SELECT COUNT(*) FROM Issues ';
		if (is_numeric($p_publicationId)) {
			$queryStr .= " WHERE IdPublication=$p_publicationId";
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetNumIssues


	/**
	 * Return an issue number that is not in use.
	 * @param int $p_publicationId
	 * @return int
	 */
	function GetUnusedIssueId($p_publicationId)
	{
		global $g_ado_db;
		$queryStr = "SELECT MAX(Number) + 1 FROM Issues "
		            ." WHERE IdPublication=$p_publicationId";
		$number = $g_ado_db->GetOne($queryStr);
		return $number;
	} // fn GetUnusedIssueId


	/**
	 * Return the last Issue created in this publication or NULL if there
	 * are no previous issues.
	 *
	 * @param int $p_publicationId
	 * @return Issue
	 */
	function GetLastCreatedIssue($p_publicationId)
	{
	   global $g_ado_db;
	   $queryStr = "SELECT MAX(Number) FROM Issues WHERE IdPublication=$p_publicationId";
	   $maxIssueNumber = $g_ado_db->GetOne($queryStr);
	   if (empty($maxIssueNumber)) {
	       return null;
	   }
	   $queryStr = "SELECT IdLanguage FROM Issues WHERE IdPublication=$p_publicationId AND Number=$maxIssueNumber";
	   $idLanguage = $g_ado_db->GetOne($queryStr);
	   $issue =& new Issue($p_publicationId, $idLanguage, $maxIssueNumber);
	   return $issue;
	} // fn GetLastCreatedIssue

} // class Issue

?>