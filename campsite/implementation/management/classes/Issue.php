<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Issue extends DatabaseObject {
	var $m_dbTableName = "Issues";
	var $m_primaryKeyColumnNames = array("IdPublication", "Number", "IdLanguage");
	var $IdPublication;
	var $Number;
	var $IdLanguage;
	var $Name;
	var $PublicationDate;
	var $Published;
	var $IssueTplId;
	var $SectionTplId;
	var $ArticleTplId;
	var $ShortName;	
	
	function Issue($p_publicationId, $p_languageId, $p_issueId = null) {
		parent::DatabaseObject();
		$this->IdPublication = $p_publicationId;
		$this->IdLanguage = $p_languageId;
		$this->Number = $p_issueId;
		if (!is_null($p_issueId)) {
			$this->fetch();
		}
	} // ctor

	function getAllIssues() {
		global $Campsite;
		$queryStr = "SELECT * FROM Issues ";
		$query = $Campsite["db"]->Execute($queryStr);
		$issues = array();
		while ($row = $query->FetchRow($queryStr)) {
			$tmpIssue =& new Issue();
			$tmpIssue->fetch($row);
			$issues[] = $tmpIssue;
		}
		return $issues;		
	} // fn getAllIssues
	
	function getIssuesInPublication($p_publicationId, $p_languageId) {
		global $Campsite;
		$queryStr = "SELECT * FROM Issues "
					." WHERE IdPublication='".$p_publicationId."'"
					." AND IdLanguage='".$p_languageId."'";
		$query = $Campsite["db"]->Execute($queryStr);
		$issues = array();
		while ($row = $query->FetchRow()) {
			$tmpIssue =& new Issue($row["IdPublication"], $row["IdLanguage"]);
			$tmpIssue->fetch($row);
			$issues[] = $tmpIssue;
		}
		return $issues;
	} // fn getAllIssuesInPublication
	
	function getPublicationId() {
		return $this->IdPublication;
	} // fn getPublicationId
	
	
	function getLanguageId() {
		return $this->IdLanguage;
	} // fn getLanguageId
	
	
	function getIssueId() {
		return $this->Number;
	} // fn getIssueId
	
	
	function getName() {
		return $this->Name;
	} // fn getName
	
	function getShortName() {
		return $this->ShortName;
	}
	
	function getArticleTemplateId() {
		return $this->ArticleTplId;
	}
	
	function getSectionTemplateId() {
		return $this->SectionTplId;
	}
	
	function getIssueTemplateId() {
		return $this->IssueTplId;
	}
} // class Issue

?>