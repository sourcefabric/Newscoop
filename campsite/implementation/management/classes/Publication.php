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
class Publication extends DatabaseObject {
	var $m_dbTableName = 'Publications';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'IdDefaultLanguage', 'PayTime', 'TimeUnit', 'UnitCost', 'Currency', 'TrialTime', 'PaidTime', 'IdDefaultAlias', 'IdURLType');
	
	/**
	 * @param int $p_publicationId
	 */
	function Publication($p_publicationId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_publicationId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor

	
	/**
	 * @return int
	 */
	function getPublicationId() 
	{
		return $this->getProperty('Id');
	} // fn getPublicationId
	
	
	/**
	 * @return string
	 */
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName

	
	/**
	 * @return int
	 */
	function getLanguageId() 
	{
		return $this->getProperty('IdDefaultLanguage');
	} // fn getLanguageId
	
	
	/**
	 * Return all publications as an array of Publication objects.
	 * @return array
	 */
	function GetAllPublications() 
	{
		$queryStr = 'SELECT * FROM Publications ORDER BY Name';
		$publications =& DbObjectArray::Create('Publication', $queryStr);
		return $publications;
	} // fn getAllPublications
	
} // class Publication
?>