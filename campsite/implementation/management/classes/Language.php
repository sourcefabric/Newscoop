<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Language extends DatabaseObject {
	var $m_dbTableName = "Languages";
	var $m_primaryKeyColumnNames = array("Id");
	var $Id;
	var $Name;
	var $Code;
	
	function Language($p_languageId = null) {
		parent::DatabaseObject();
		$this->Id = $p_languageId;
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
		return $this->Id;
	} // fn getLanguageId
	
	
	function getName() {
		return $this->Name;
	} // fn getName
	
	function getCode() {
		return $this->Code;
	} // fn getCode
	
} // class Language

?>