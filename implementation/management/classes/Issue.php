<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Issue extends DatabaseObject {
	var $m_dbTableName = "Issues";
	var $m_primaryKeyColumnNames = array("IdPublication", "Number", "IdLanguage");
	var $m_columnNames = array("IdPublication",
							   "Number",
							   "IdLanguage",
							   "Name",
							   "PublicationDate",
							   "Published",
							   "IssueTplId",
							   "SectionTplId",
							   "ArticleTplId",
							   "ShortName");
	
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
		$this->IdPublication = $p_publicationId;
		$this->IdLanguage = $p_languageId;
		$this->Number = $p_issueId;
		if (!is_null($p_issueId)) {
			$this->fetch();
		}
	} // ctor

	function getPublicationId() {
		return $this->IdPublication;
	}
	
	function getLanguageId() {
		return $this->IdLanguage;
	}
	
	function getIssueId() {
		return $this->Number;
	}
	
	function getName() {
		return $this->Name;
	}
	
} // class Issue

?>