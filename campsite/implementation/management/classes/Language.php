<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");

class Language extends DatabaseObject {
	var $m_dbTableName = "Languages";
	var $m_primaryKeyColumnNames = array("Id");
	var $m_columnNames = array("Id","Name");

	var $Id;
	var $Name;
	
	function Language($p_languageId = null) {
		$this->Id = $p_languageId;
		if (!is_null($p_languageId)) {
			$this->fetch();
		}
	} // constructor
	

	function getLanguageId() {
		return $this->Id;
	} // fn getLanguageId
	
	
	function getName() {
		return $this->Name;
	} // fn getName
	
} // class Language

?>