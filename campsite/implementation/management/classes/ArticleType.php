<?php
/**
 * @package Campsite
 */

/** 
 * Includes
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbColumn.php');

/**
 * @package Campsite
 */
class ArticleType extends DatabaseObject {
	var $m_columnNames = array('NrArticle', 'IdLanguage');
	var $m_keyColumnNames = array('NrArticle', 'IdLanguage');
	var $m_dbTableName;
	
	/**
	 * An article type is a dynamic table that is created for an article
	 * to allow different publications to display their content in different
	 * ways.  
	 *
	 * @param string $p_articleType
	 * @param int $p_articleId
	 * @param int $p_languageId
	 */
	function ArticleType($p_articleType, $p_articleId, $p_languageId) 
	{
		$this->m_dbTableName = 'X'.$p_articleType;
		// Get user-defined values.
		$dbColumns = $this->getUserDefinedColumns();
		foreach ($dbColumns as $columnMetaData) {
			$this->m_columnNames[] = $columnMetaData->getName();
		}
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['NrArticle'] = $p_articleId;
		$this->m_data['IdLanguage'] = $p_languageId;
		if ($this->exists()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * Copy the row in the database.
	 * @param int $p_destArticleId
	 * @return void
	 */
	function copy($p_destArticleId) 
	{
		global $Campsite;
		$tmpData = $this->m_data;
		unset($tmpData['NrArticle']);
		foreach ($tmpData as $key => $data) {
			$tmpData[$key] = "'".$data."'";
		}
		
		$queryStr = 'INSERT IGNORE INTO '.$this->m_dbTableName
			.'(NrArticle,'.implode(',', array_keys($this->m_columnNames)).')'
			.' VALUES ('.$p_destArticleId.','.implode(',', $tmpData).')';
		$Campsite['db']->Execute($queryStr);
	} // fn copy
	
	
	/**
	 * Copy the row in the database.
	 * @param int $p_destArticleId
	 * @param int $p_destLanguageId
	 * @return void
	 */
	function copyToExistingRecord($p_destArticleId, $p_destLanguageId = null) 
	{
		global $Campsite;
		$tmpData = $this->m_data;
		unset($tmpData['NrArticle']);
		unset($tmpData['IdLanguage']);
		$setQuery = array();
		foreach ($tmpData as $key => $data) {
			$setQuery[] = $key."='".mysql_real_escape_string($data)."'";
		}		
		$queryStr = 'UPDATE '.$this->m_dbTableName.' SET '.implode(',', $setQuery)
				." WHERE NrArticle=$p_destArticleId ";
		if (!is_null($p_destLanguageId)) {
			$queryStr .= " AND IdLanguage=".$p_destLanguageId;
		}
		else {
			$queryStr .= " AND IdLanguage=".$this->m_data['IdLanguage'];
		}
		$Campsite['db']->Execute($queryStr);
	} // fn copyToExistingRecord
	
	
	/**
	 * Return an array of DbColumn objects.
	 *
	 * @return array
	 */
	function getUserDefinedColumns() 
	{
		global $Campsite;
		$queryStr = 'SHOW COLUMNS FROM '.$this->m_dbTableName
					." LIKE 'F%'";
		$queryArray = $Campsite['db']->GetAll($queryStr);
		$metadata = array();
		if (is_array($queryArray)) {
			foreach ($queryArray as $row) {
				$columnMetadata =& new DbColumn($this->m_dbTableName);
				$columnMetadata->fetch($row);
				$metadata[] =& $columnMetadata;
			}
		}
		return $metadata;
	} // fn getUserDefinedColumns

	
	/**
	 * Get all article types that currently exist.
	 * Returns an array of strings.
	 *
	 * @return array
	 */ 
	function GetAllTypes() 
	{
		global $Campsite;
		$queryStr = "SHOW TABLES LIKE 'X%'";
		$tableNames = $Campsite['db']->GetCol($queryStr);
		if (!is_array($tableNames)) {
			$tableNames = array();
		}
		$finalNames = array();
		foreach ($tableNames as $tmpName) {
			$finalNames[] = substr($tmpName, 1);
		}
		return $finalNames;
	} // fn GetAllTypes
	
} // class ArticleType

?>