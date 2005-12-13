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
require_once($g_documentRoot.'/classes/Log.php');

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
	
	
	function create($p_values = null)
	{
		$success = parent::create($p_values);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Country $1 added', $this->m_data['Name']." (".$this->m_data['Code'].")");
			Log::Message($logtext, null, 131);		
		}
	} // fn create
	
	
	function delete()
	{
		$success = parent::delete();
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Country $1 deleted', $this->m_data['Name'].' ('.$this->m_data['Code'].')' ); 
			Log::Message($logtext, null, 134);
		}		
	} // fn delete
	
	
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
	 * Set the name of the country.
	 * @param string $p_value
	 * @return boolean
	 */
	function setName($p_value)
	{
		$oldValue = $this->m_data['Name'];
		$success = $this->setProperty('Name', $p_value);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Country name $1 changed', $this->m_data['Name']." (".$this->m_data['Code'].")");
			Log::Message($logtext, null, 133);
		}
	} // fn setName
	
	
	/**
	 * Get the two-letter code for this language.
	 * @return string
	 */
	function getCode() 
	{
		return $this->getProperty('Code');
	} // fn getCode
	
	
	/**
	 *
	 *
	 */
	function GetNumCountries($p_languageId = null, $p_code = null, $p_name = null)
	{
		global $Campsite;
		$queryStr = "SELECT COUNT(*) FROM Countries";
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
		if (count($constraints) > 0) {
			$tmpArray = array();
			foreach ($constraints as $constraint) {
				$tmpArray[] = $constraint[0]."='".$constraint[1]."'";
			}
			$queryStr .= " WHERE ".implode(" AND ", $tmpArray);
		}
		$total = $Campsite['db']->GetOne($queryStr);
		return $total;
	} // fn GetNumCountries
	
	
	/**
	 * @param int $p_languageId
	 * @param string $p_code
	 * @param string $p_name
	 * @param array $p_sqlOptions
	 * @return array
	 */
	function GetCountries($p_languageId = null, $p_code = null, $p_name = null, $p_sqlOptions = null)
	{
		if (is_null($p_sqlOptions)) {
			$p_sqlOptions = array();
		}
		if (!isset($p_sqlOptions["ORDER BY"])) {
			$p_sqlOptions["ORDER BY"] = array("Code", "IdLanguage");
		}
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
		return DatabaseObject::Search('Country', $constraints, $p_sqlOptions);
	} // fn GetCountries
	
} // class Country

?>