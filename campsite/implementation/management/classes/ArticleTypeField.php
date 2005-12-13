<?php

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/ParserCom.php');

/**
 * @package Campsite
 */
class ArticleTypeField {
	var $m_dbTableName;
	var $m_articleTypeName;
	var $m_dbColumnName;
	var $m_fieldName;
	var $Field;
	var $Type;
	var $Null;
	var $Key;
	var $Default;
	var $Extra;
	
	function ArticleTypeField($p_articleTypeName = null, $p_fieldName = null) 
	{
		$this->m_articleTypeName = $p_articleTypeName;
		$this->m_fieldName = $p_fieldName;
		$this->m_dbTableName = "X".$p_articleTypeName;
		$this->m_dbColumnName = "F".$p_fieldName;
		if (!is_null($this->m_articleTypeName) && !is_null($this->m_fieldName)) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * @return string
	 */
	function getDbTableName() 
	{
		return $this->m_dbTableName;
	} // fn getDbTableName
	
	
	/**
	 * Create a column in the table.
	 * @param string $p_type
	 *		Can be one of: 'text', 'date', 'body'.
	 */
	function create($p_type)
	{
		global $Campsite;
		$p_type = strtolower($p_type);
		$queryStr = "ALTER TABLE ".$this->m_dbTableName." ADD COLUMN ".$this->m_dbColumnName;
		switch ($p_type) {
			case 'text':
			    $queryStr .= " VARCHAR(255) NOT NULL";
			    break;
			case 'date':
		    	$queryStr .= " DATE NOT NULL";
		    	break;
			case 'body':
		    	$queryStr .= " MEDIUMBLOB NOT NULL";
		    	break;
		    default:
		    	return;
		}
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Article type field $1 created', $this->m_dbColumnName);
			Log::Message($logtext, null, 71);
			ParserCom::SendMessage('article_types', 'create', array("article_type"=> $this->m_articleTypeName));
		}
	} // fn create
	
	
	/**
	 * @return boolean
	 */
	function exists()
	{
		global $Campsite;
		$queryStr = "SHOW COLUMNS FROM ".$this->m_dbTableName." LIKE '".$this->m_dbColumnName."'";
		$exists = $Campsite['db']->GetOne($queryStr);
		if ($exists) {
			return true;
		}
		else {
			return false;
		}
	} // fn exists
	
	
	/**
	 * @return void
	 */
	function fetch($p_recordSet = null) 
	{
		global $Campsite;
		if (!is_null($p_recordSet)) {
			foreach ($p_recordSet as $key => $value) {
				$this->$key = $value;
			}
		}
		else {
			$queryStr = 'SHOW COLUMNS FROM '.$this->m_dbTableName
						." LIKE 'F".$this->m_dbColumnName."'";
			$row = $Campsite['db']->GetAll($queryStr);
			$this->fetch($row);
		}
	} // fn fetch
	
	
	function delete()
	{
		global $Campsite;
		$queryStr = "ALTER TABLE ".$this->m_dbTableName." DROP COLUMN ".$this->m_dbColumnName;
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Article type field $1 deleted', $this->m_dbColumnName); 
			Log::Message($logtext, null, 72);
			ParserCom::SendMessage("article_types", "modify", array("article_type"=>"$AType"));
		}
	} // fn delete
	
	
	/**
	 * @return string
	 */
	function getName() 
	{
		return $this->Field;
	} // fn getName
	

	/**
	 * @return string
	 */
	function getPrintName() 
	{
		return substr($this->Field, 1);
	} // fn getPrintName
	
	
	/**
	 * @return string
	 */
	function getType() 
	{
		return strtolower($this->Type);
	} // fn getType
	
	
	/**
	 * Get a human-readable representation of the column type.
	 * @return string
	 */
	function getPrintType()
	{
		switch ($this->Type) {
	    case 'mediumblob':
	    	return getGS('Article body');
	    case 'varchar(255)':
	    	return getGS('Text');
	    case 'varbinary(255)':
	    	return getGS('Text');
	    case 'date':
	    	return getGS('Date');
	    default:
	    	return "unknown";
		}
	} // fn getPrintType
	
} // class ArticleTypeField


?>