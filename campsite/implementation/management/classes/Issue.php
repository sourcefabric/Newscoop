<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

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
	 * Get all issues in the database.
	 * @return array
	 */
	function GetAllIssues() {
		//global $Campsite;
		$queryStr = 'SELECT * FROM Issues ';
		$issues =& DbObjectArray::Create('Issue', $queryStr);
//		$query = $Campsite['db']->Execute($queryStr);
//		$issues = array();
//		while ($row = $query->FetchRow($queryStr)) {
//			$tmpIssue =& new Issue();
//			$tmpIssue->fetch($row);
//			$issues[] = $tmpIssue;
//		}
		return $issues;		
	} // fn GetAllIssues
	
	
	/**
	 * Get all the issues in the given publication as return them as an array 
	 * of Issue objects.
	 *
	 * @param int p_publicationId
	 * @param int p_languageId
	 * @param array p_sqlOptions
	 *
	 * @return array
	 */
	function GetIssuesInPublication($p_publicationId, $p_languageId = null, $p_sqlOptions = null) {
		$queryStr = 'SELECT * FROM Issues '
					." WHERE IdPublication='".$p_publicationId."'";
		if (!is_null($p_languageId)) {
			$queryStr .= " AND IdLanguage='".$p_languageId."'";
		}
		$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$issues =& DbObjectArray::Create('Issue', $queryStr);
		return $issues;
	} // fn GetAllIssuesInPublication
	
	
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
} // class Issue

?>