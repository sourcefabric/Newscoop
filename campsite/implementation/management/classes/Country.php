<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class Country extends DatabaseObject {
	var $m_dbTableName = 'Countries';
	var $m_keyColumnNames = array('Code', 'IdLanguage');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array('Code', 'IdLanguage', 'Name');
	
	/** 
	 * Constructor.
	 * @param string $p_code
	 * @param int $p_languageId
	 */
	function Country($p_code = null, $p_languageId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Code'] = $p_code;
		$this->m_data['IdLanguage'] = $p_languageId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * The unique ID of the language in the database.
	 * @return int
	 */
	function getLanguageId() 
	{
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	/**
	 * Return the english name of this language.
	 * @return string
	 */
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	

	/**
	 * Get the two-letter code for this language.
	 * @return string
	 */
	function getCode() 
	{
		return $this->getProperty('Code');
	} // fn getCode
	
	
	/**
	 * @param int $p_languageId
	 * @param string $p_code
	 * @param string $p_name
	 * @return array
	 */
	function GetCountries($p_languageId = null, $p_code = null, $p_name = null)
	{
		$constraints = array();
		if (!is_null($p_languageId)) {
			$constraints[] = array('IdLanguage', $p_languageId);
		}
		if (!is_null($p_code)) {
			$constraints[] = array('Code', $p_code);
		}
		if (!is_null($p_name)) {
			$constraints[] = array('Name', $p_name);
		}
		return DatabaseObject::Search('Country', $constraints);
	} // fn GetCountries
	
} // class Country

?>