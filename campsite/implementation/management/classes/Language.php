<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

class Language extends DatabaseObject {
	var $m_dbTableName = 'Languages';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'Code');
	
	/** 
	 * Constructor.
	 */
	function Language($p_languageId = null) {
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('Id', $p_languageId, false);
		if (!is_null($p_languageId)) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * Return an array of all languages (array of Language objects).
	 * @return array
	 */
	function GetAllLanguages() {
		global $Campsite;
		$queryStr = 'SELECT * FROM Languages ORDER BY Name';
		$query = $Campsite['db']->Execute($queryStr);
		$languages = array();
		while ($row = $query->FetchRow()) {
			$tmpLanguage =& new Language();
			$tmpLanguage->fetch($row);
			$languages[] = $tmpLanguage;
		}
		return $languages;
	} // fn GetAllLanguages

	
	/**
	 * @return int
	 */
	function getLanguageId() {
		return $this->getProperty('Id');
	} // fn getLanguageId
	
	
	/**
	 * @return string
	 */
	function getName() {
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * @return string
	 */
	function getCode() {
		return $this->getProperty('Code');
	} // fn getCode
	
} // class Language

?>