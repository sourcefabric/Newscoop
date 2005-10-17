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
require_once($g_documentRoot.'/classes/ParserCom.php');

/**
 * @package Campsite
 */
class Publication extends DatabaseObject {
	var $m_dbTableName = 'Publications';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 
	                           'Name', 
	                           'IdDefaultLanguage', 
	                           'PayTime', 
	                           'TimeUnit', 
	                           'UnitCost', 
	                           'Currency', 
	                           'TrialTime', 
	                           'PaidTime', 
	                           'IdDefaultAlias', 
	                           'IdURLType');
	
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

	
	function create($p_values = null) 
	{
		$created = parent::create($p_values);	
		if ($created) {
			ParserCom::SendMessage('publication', 'create', array("IdPublication" => $this->m_data['Id']));
		}
		return $created;
	} // fn create
	
	
	function update($p_columns = null, $p_commit = true, $p_isSql = false)
	{
		$updated = parent::update($p_columns, $p_commit, $p_isSql);
		if ($updated) {
			ParserCom::SendMessage('publication', 'modify', array("IdPublication" => $this->m_data['Id']));
		}
		return $updated;
	} // fn update
	
	
	function delete() 
	{
		$aliases = Alias::GetAliases($this->m_data['id']);
		if ($aliases && (count($aliases) > 0)) {
			foreach ($aliases as $alias) {
				$alias->delete();
			}
		}
		$deleted = parent::delete();
		if ($deleted) {
			ParserCom::SendMessage('publication', 'delete', array("IdPublication" => $this->m_data['Id']));			
		}
		return $deleted;
	} // fn delete
	
	
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
	 * @return string
	 */
	function getTimeUnit() 
	{
		return $this->getProperty('TimeUnit');
	} // fn getTimeUnit
	

	/**
	 * @return int
	 */
	function getDefaultAliasId()
	{
		return $this->getProperty('IdDefaultAlias');
	} // fn getDefaultAliasId
	
	
	/**
	 * @return int
	 */
	function getDefaultLanguageId()
	{
		return $this->getProperty('IdDefaultLanguage');
	} // fn getDefaultLanguageId
	
	
	/**
	 * @return int
	 */
	function getUrlTypeId()
	{
		return $this->getProperty('IdURLType');
	} // fn getUrlTypeId
	
	
	/**
	 * @return int
	 */
	function getPayTime()
	{
		return $this->getProperty('PayTime');
	} // fn getPayTime
	
	
	/**
	 * @return float
	 */
	function getUnitCost()
	{
		return $this->getProperty('UnitCost');
	} // fn getUnitCost
	
	
	/**
	 * @return string
	 */
	function getCurrency() 
	{
		return $this->getProperty('Currency');
	} // fn getCurrency
	
	
	/**
	 * @return int
	 */
	function getPaidTime()
	{
		return $this->getProperty('PaidTime');
	} // fn getPaidTime
	
	
	/**
	 * @return int
	 */
	function getTrialTime()
	{
		return $this->getProperty('TrialTime');
	} // fn getTrialTime
	
	
	/**
	 * Return the total number of publications.
	 * @return int
	 */
	function GetNumPublications()
	{
	    global $Campsite;
	    $queryStr = "SELECT COUNT(*) FROM Publications";
	    return $Campsite['db']->GetOne($queryStr);
	} // fn GetNumPublications
	
	
	/**
	 * Return all publications as an array of Publication objects.
	 * @return array
	 */
	function GetPublications($p_sqlOptions = null) 
	{
	    if (is_null($p_sqlOptions)) {
	        $p_sqlOptions = array();
	    }
	    if (!isset($p_sqlOptions["ORDER BY"])) {
	        $p_sqlOptions["ORDER BY"] = array("Name" => "ASC");
	    }
	    $tmpPub =& new Publication();
	    $columns = $tmpPub->getColumnNames(true);
		$queryStr = 'SELECT '.implode(',', $columns)
		            .', Aliases.Name as Alias'
		            .', URLTypes.Name as URLType'
		            .', Languages.OrigName as NativeName'
		            .' FROM Publications, Languages, Aliases, URLTypes'
		            .' WHERE Publications.IdDefaultAlias = Aliases.Id '
		            .' AND Publications.IdURLType = URLTypes.Id '
		            .' AND Publications.IdDefaultLanguage = Languages.Id ';
        $queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);		            
		$publications =& DbObjectArray::Create('Publication', $queryStr);
		return $publications;
	} // fn getPublications
	
} // class Publication
?>