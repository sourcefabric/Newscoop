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
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');

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
	 * 
	 * @param int $p_publicationId
	 * @param int $p_languageId
	 * @param int $p_issueId
	 */
	function Issue($p_publicationId = null, $p_languageId = null, $p_issueId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_issueId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor

	
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
		global $Campsite;
		$queryStr = 'SELECT COUNT(*) FROM Issues ';
		if (is_numeric($p_publicationId)) {
			$queryStr .= " WHERE IdPublication=$p_publicationId";
		}
		$total = $Campsite['db']->GetOne($queryStr);
		return $total;				
	} // fn GetNumIssues

	
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
		global $Campsite;
		$issues = array();
		$rows = $Campsite['db']->GetAll($queryStr);
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
	 * @return int
	 */
	function getPublicationId() 
	{
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getLanguageId() 
	{
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId

	
	/**
	 * A simple way to get the name of the language the article is 
	 * written in.  The value is cached in case there are multiple
	 * calls to this function.
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
	function getIssueId() 
	{
		return $this->getProperty('Number');
	} // fn getIssueId
	
	
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
	
	
	/**
	 * @return int
	 */
	function getIssueTemplateId() 
	{
		return $this->getProperty('IssueTplId');
	} // fn getIssueTemplateId
	
	
	function getPublished() 
	{
		return $this->getProperty('Published');
	} // fn getPublished
	
	
	/**
	 * Set the published state of the issue.
	 *
	 * @param string $p_value
	 *		Can be NULL, 'Y', 'N', TRUE, FALSE.
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
			}
			else {
				$doPublish = true;
			}
		}
		else {
			if (is_string($p_value)) {
				$p_value = strtoupper($p_value);
			}
			if (($this->m_data['Published'] == 'N') && (($p_value == 'Y') || ($p_value === true))) {
				$doPublish = true;
			}
			elseif (($this->m_data['Published'] == 'Y') && (($p_value == 'N') || ($p_value === false))) {
				$doPublish = false;
			}
		}
		if (!is_null($doPublish)) {
			if ($doPublish) {
				$this->setProperty('Published', 'Y', true);
				$this->setProperty('PublicationDate', 'NOW()', true, true);
			}
			else {
				$this->setProperty('Published', 'N', true);			
			}
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
		$languages =& DbObjectArray::Create('Language', $queryStr);
		return $languages;
	} // fn getUsusedLanguages
	
} // class Issue

?>