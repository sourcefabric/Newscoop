<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

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
	
	function Issue($p_publicationId, $p_languageId, $p_issueId = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('IdPublication', $p_publicationId, false);
		$this->setProperty('IdLanguage', $p_languageId, false);
		$this->setProperty('Number', $p_issueId, false);
		if (!is_null($p_issueId)) {
			$this->fetch();
		}
	} // ctor

	
	/**
	 * Get all issues in the database.
	 * @return array
	 */
	function GetAllIssues() {
		global $Campsite;
		$queryStr = 'SELECT * FROM Issues ';
		$query = $Campsite['db']->Execute($queryStr);
		$issues = array();
		while ($row = $query->FetchRow($queryStr)) {
			$tmpIssue =& new Issue();
			$tmpIssue->fetch($row);
			$issues[] = $tmpIssue;
		}
		return $issues;		
	} // fn GetAllIssues
	
	
	function GetIssuesInPublication($p_publicationId, $p_languageId = null) {
		global $Campsite;
		$queryStr = 'SELECT * FROM Issues '
					." WHERE IdPublication='".$p_publicationId."'";
		if (!is_null($p_languageId)) {
			$queryStr .= " AND IdLanguage='".$p_languageId."'";
		}
		$query = $Campsite['db']->Execute($queryStr);
		$issues = array();
		while ($row = $query->FetchRow()) {
			$tmpIssue =& new Issue($row['IdPublication'], $row['IdLanguage']);
			$tmpIssue->fetch($row);
			$issues[] = $tmpIssue;
		}
		return $issues;
	} // fn GetAllIssuesInPublication
	
	
	function getPublicationId() {
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	function getLanguageId() {
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	function getIssueId() {
		return $this->getProperty('Number');
	} // fn getIssueId
	
	
	function getName() {
		return $this->getProperty('Name');
	} // fn getName
	
	
	function getShortName() {
		return $this->getProperty('ShortName');
	}
	
	
	function getArticleTemplateId() {
		return $this->getProperty('ArticleTplId');
	}
	
	
	function getSectionTemplateId() {
		return $this->getProperty('SectionTplId');
	}
	
	
	function getIssueTemplateId() {
		return $this->getProperty('IssueTplId');
	}
} // class Issue

?>