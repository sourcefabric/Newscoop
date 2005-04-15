<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');

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
	 * @param int p_publicationId
	 * @param int p_languageId
	 * @param int p_issueId
	 */
	function Issue($p_publicationId = null, $p_languageId = null, $p_issueId = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('IdPublication', $p_publicationId, false);
		$this->setProperty('IdLanguage', $p_languageId, false);
		$this->setProperty('Number', $p_issueId, false);
		if (!is_null($p_publicationId) && !is_null($p_languageId) && !is_null($p_issueId)) {
			$this->fetch();
		}
	} // constructor

	
	/**
	 * Return the total number of issues in the database.
	 *
	 * @param int p_publicationId
	 *		If specified, return the total number of issues in the given publication.
	 *
	 * @return int
	 */
	function GetNumIssues($p_publicationId = null) {
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
	 * @param int p_publicationId
	 * 		The publication ID.
	 *
	 * @param int p_languageId
	 *		(Optional) Only show issues for this language.
	 *
	 * @param int p_preferredLanguage
	 *		(Optional) List this language before others.  This will override any 'ORDER BY' sql
	 *		options you have.
	 *
	 * @param array p_sqlOptions
	 *
	 * @return array
	 */
	function GetIssues($p_publicationId = null, $p_languageId = null, $p_preferredLanguage = null, $p_sqlOptions = null) {
		$queryStr = 'SELECT * ';
		if (!is_null($p_preferredLanguage)) {
			$tmpIssue =& new Issue();
			$columnNames = $tmpIssue->getColumnNames();
			$queryStr = 'SELECT '.implode(',', $columnNames)
				.", abs(IdLanguage-$p_preferredLanguage) as LanguageOrder";
			$p_sqlOptions['ORDER BY'] = array('Number' => 'DESC', 'LanguageOrder' => 'ASC');
		}
		
		$queryStr .= ' FROM Issues ';
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$issues =& DbObjectArray::Create('Issue', $queryStr);
		return $issues;
	} // fn GetIssues
		
	
	/**
	 * @return int
	 */
	function getPublicationId() {
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getLanguageId() {
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId

	
	/**
	 * A simple way to get the name of the language the article is 
	 * written in.  The value is cached in case there are multiple
	 * calls to this function.
	 */
	function getLanguageName() {
		if (is_null($this->m_languageName)) {
			$language =& new Language($this->m_data['IdLanguage']);
			$this->m_languageName = $language->getNativeName();
		}
		return $this->m_languageName;		
	} // fn getLanguageName
	
	
	/**
	 * @return int
	 */
	function getIssueId() {
		return $this->getProperty('Number');
	} // fn getIssueId
	
	
	/**
	 * @return string
	 */
	function getName() {
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * @return string
	 */
	function getShortName() {
		return $this->getProperty('ShortName');
	} // fn getShortName
	
	
	/**
	 * @return int
	 */
	function getArticleTemplateId() {
		return $this->getProperty('ArticleTplId');
	} // fn getArticleTemplateId
	
	
	/**
	 * @return int
	 */
	function getSectionTemplateId() {
		return $this->getProperty('SectionTplId');
	} // fn getSectionTemplateId
	
	
	/**
	 * @return int
	 */
	function getIssueTemplateId() {
		return $this->getProperty('IssueTplId');
	} // fn getIssueTemplateId
	
	
	function getPublished() {
		return $this->getProperty('Published');
	}
	
	
	function getPublicationDate() {
		return $this->getProperty('PublicationDate');
	}
} // class Issue

?>