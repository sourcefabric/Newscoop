<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Language extends DatabaseObject {
	var $m_dbTableName = "Languages";
	var $m_keyColumnNames = array("Id");
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array("Id", "Name", "Code");
	
	function Language($p_languageId = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty("Id", $p_languageId, false);
		if (!is_null($p_languageId)) {
			$this->fetch();
		}
	} // constructor
	
	function getAllLanguages() {
		global $Campsite;
		$queryStr = "SELECT * FROM Languages";
		$query = $Campsite["db"]->Execute($queryStr);
		$languages = array();
		while ($row = $query->FetchRow()) {
			$tmpLanguage =& new Language();
			$tmpLanguage->fetch($row);
			$languages[] = $tmpLanguage;
		}
		return $languages;
	} // fn getAllLanguages

	function getLanguageId() {
		return $this->getProperty("Id");
	} // fn getLanguageId
	
	
	function getName() {
		return $this->getProperty("Name");
	} // fn getName
	
	function getCode() {
		return $this->getProperty("Code");
	} // fn getCode
	
} // class Language

?>