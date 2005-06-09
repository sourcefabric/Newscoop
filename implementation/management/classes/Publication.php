<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

class Publication extends DatabaseObject {
	var $m_dbTableName = 'Publications';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'IdDefaultLanguage', 'PayTime', 'TimeUnit', 'UnitCost', 'Currency', 'TrialTime', 'PaidTime', 'IdDefaultAlias', 'IdURLType');
	
	function Publication($p_publicationId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_publicationId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor

	
	function getPublicationId() 
	{
		return $this->getProperty('Id');
	} // fn getPublicationId
	
	
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName

	
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