<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Section extends DatabaseObject {
	var $m_dbTableName = "Sections";
	var $m_primaryKeyColumnNames = array("IdPublication", 
										 "NrIssue",
										 "IdLanguage",
										 "Number");
//	var $m_columnNames = array("IdPublication", 
//							   "NrIssue",
//							   "IdLanguage",
//							   "Number",
//							   "Name",
//							   "ShortName",
//							   "SectionTplId",
//							   "ArticleTplId"
//							   );
	var $IdPublication;
	var $NrIssue;
	var $IdLanguage;
	var $Number;
	var $Name;
	var $ShortName;
	var $SectionTplId;
	var $ArticleTplId;
	
	function Section($p_publication, $p_issue, $p_language, $p_section = null) {
		parent::DatabaseObject();
		$this->IdPublication = $p_publication;
		$this->NrIssue = $p_issue;
		$this->IdLanguage = $p_language;
		$this->Number = $p_section;
		if (!is_null($p_section)) {
			$this->fetch();
		}
	} // fn Section

	
	function getPublicationId() {
		return $this->IdPublication;
	} // fn getPublicationId
	
	
	function getIssueId() {
		return $this->NrIssue;
	} // fn getIssueId
	
	
	function getLanguageId() {
		return $this->IdLanguage;
	} // fn getLanguageId
	
	
	function getSectionId() {
		return $this->Number;
	} // fn getSectionId

	
	function getName() {
		return $this->Name;
	} // fn getName
	
	
	function getShortName() {
		return $this->ShortName;
	} // fn getShortName
	
} // class Section
?>