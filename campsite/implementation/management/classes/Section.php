<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class Section extends DatabaseObject {
	var $m_dbTableName = 'Sections';
	var $m_keyColumnNames = array('IdPublication', 
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
		'SectionTplId',
		'ArticleTplId');
	
	/**
	 * A section is a part of an issue.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @param int $p_sectionId
	 */
	function Section($p_publicationId = null, $p_issueId = null, 
	                 $p_languageId = null, $p_sectionId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['NrIssue'] = $p_issueId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_sectionId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // fn Section

	
	/**
	 * Return an array of sections in the given issue.
	 * @param int $p_publicationId
	 * 		(Optional) Only return sections in this publication.
	 *
	 * @param int $p_issueId
	 *		(Optional) Only return sections in this issue.
	 *
	 * @param int $p_languageId
	 * 		(Optional) Only return sections that have this language.
	 *
	 * @param array $p_sqlOptions
	 *		(Optional) Additional options.  See DatabaseObject::ProcessOptions().
	 *
	 * @return array
	 */
	function GetSections($p_publicationId = null, $p_issueId = null,
	                     $p_languageId = null, $p_sqlOptions = null) 
	{
		$queryStr = 'SELECT * FROM Sections';
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_issueId)) {
			$whereClause[] = "NrIssue=$p_issueId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		if (!is_null($p_sqlOptions)) {
			$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		}
		$sections = DbObjectArray::Create('Section', $queryStr);
		return $sections;		
	} // fn GetSectionsInIssue
	
	
	/**
	 * Return the total number of sections according to the given values.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @return int
	 */
	function GetTotalSections($p_publicationId = null, $p_issueId = null, $p_languageId = null) 
	{
		global $Campsite;
		$queryStr = 'SELECT COUNT(*) FROM Sections';
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_issueId)) {
			$whereClause[] = "NrIssue=$p_issueId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		$total = $Campsite['db']->GetOne($queryStr);
		return $total;
	} // fn GetTotalSections
	
	
	/**
	 * Return a section number that is not in use.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @return int
	 */
	function GetUnusedSectionId($p_publicationId, $p_issueId, $p_languageId) 
	{
		global $Campsite;
		$queryStr = "SELECT MAX(Number) + 1 FROM Sections "
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueId AND IdLanguage=$p_languageId";
		$number = $Campsite['db']->GetOne($queryStr);
		return $number;
	} // fn GetUnusedSectionId
	
	
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
	function getLanguageId() 
	{
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	/**
	 * @return int
	 */
	function getSectionId() 
	{
		return $this->getProperty('Number');
	} // fn getSectionId

	
	/**
	 * @return string
	 */ 
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * @return string
	 */
	function getShortName() 
	{
		return $this->getProperty('ShortName');
	} // fn getShortName
	
	
	/**
	 * @return int
	 */
	function getArticleTemplateId() 
	{
		return $this->getProperty('ArticleTplId');
	} // fn getArticleTemplateId
	
	
	/**
	 * @return int
	 */
	function getSectionTemplateId() 
	{
		return $this->getProperty('SectionTplId');
	} // fn getSectionTemplateId
	
} // class Section
?>