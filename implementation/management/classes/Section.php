<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

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
	
	function Section($p_publication, $p_issue, $p_language, $p_section = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('IdPublication', $p_publication, false);
		$this->setProperty('NrIssue', $p_issue, false);
		$this->setProperty('IdLanguage', $p_language, false);
		$this->setProperty('Number', $p_section, false);
		if (!is_null($p_section)) {
			$this->fetch();
		}
	} // fn Section

	
	function GetSectionsInIssue($p_publicationId, $p_issueId, $p_languageId) {
		global $Campsite;
		$queryStr = 'SELECT * FROM Sections'
					." WHERE IdPublication='".$p_publicationId."'"
					." AND NrIssue='".$p_issueId."'"
					." AND IdLanguage='".$p_languageId."'";
		$query = $Campsite['db']->Execute($queryStr);
		$sections = array();
		while ($row = $query->FetchRow()) {
			$tmpSection =& new Section($row['IdPublication'], $row['NrIssue'], $row['IdLanguage']);
			$tmpSection->fetch($row);
			$sections[] = $tmpSection;
		}
		return $sections;		
	} // fn GetSectionsInIssue
	
	function getPublicationId() {
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	function getIssueId() {
		return $this->getProperty('NrIssue');
	} // fn getIssueId
	
	
	function getLanguageId() {
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	function getSectionId() {
		return $this->getProperty('Number');
	} // fn getSectionId

	
	function getName() {
		return $this->getProperty('Name');
	} // fn getName
	
	
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
} // class Section
?>