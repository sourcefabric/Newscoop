<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Section extends DatabaseObject {
	var $m_dbTableName = "Sections";
	var $m_primaryKeyColumnNames = array("IdPublication", 
										 "NrIssue",
										 "IdLanguage",
										 "Number");
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

	
	function getSectionsInIssue($p_publicationId, $p_issueId, $p_languageId) {
		global $Campsite;
		$queryStr = "SELECT * FROM Sections"
					." WHERE IdPublication='".$p_publicationId."'"
					." AND NrIssue='".$p_issueId."'"
					." AND IdLanguage='".$p_languageId."'";
		$query = $Campsite["db"]->Execute($queryStr);
		$sections = array();
		while ($row = $query->FetchRow()) {
			$tmpSection =& new Section($row["IdPublication"], $row["NrIssue"], $row["IdLanguage"]);
			$tmpSection->fetch($row);
			$sections[] = $tmpSection;
		}
		return $sections;		
	} // fn getAllSectionsInIssue
	
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