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
/** 
 * Includes
 */
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/ArticleTypeField.php');

/**
 * @package Campsite
 */
class ArticleData extends DatabaseObject {
	var $m_columnNames = array('NrArticle', 'IdLanguage');
	var $m_keyColumnNames = array('NrArticle', 'IdLanguage');
	var $m_dbTableName;
	var $m_articleTypeName;
	
	/**
	 * An article type is a dynamic table that is created for an article
	 * to allow different publications to display their content in different
	 * ways.  
	 *
	 * @param string $p_articleType
	 * @param int $p_articleNumber
	 * @param int $p_languageId
	 */
	function ArticleData($p_articleType, $p_articleNumber, $p_languageId) 
	{
		$this->m_articleTypeName = $p_articleType;
		$this->m_dbTableName = 'X'.$p_articleType;
		// Get user-defined values.
		$dbColumns = $this->getUserDefinedColumns();
		foreach ($dbColumns as $columnMetaData) {
			$this->m_columnNames[] = $columnMetaData->getName();
		}
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['NrArticle'] = $p_articleNumber;
		$this->m_data['IdLanguage'] = $p_languageId;
		if ($this->exists()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * Copy the row in the database.
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function copy($p_destArticleNumber) 
	{
		global $Campsite;
		$tmpData = $this->m_data;
		unset($tmpData['NrArticle']);
		foreach ($tmpData as $key => $data) {
			$tmpData[$key] = "'".$data."'";
		}
		
		$queryStr = 'INSERT IGNORE INTO '.$this->m_dbTableName
			.'(NrArticle,'.implode(',', array_keys($this->m_columnNames)).')'
			.' VALUES ('.$p_destArticleNumber.','.implode(',', $tmpData).')';
		$Campsite['db']->Execute($queryStr);
	} // fn copy
	
	
	/**
	 * Copy the row in the database.
	 * @param int $p_destArticleNumber
	 * @param int $p_destLanguageId
	 * @return void
	 */
	function copyToExistingRecord($p_destArticleNumber, $p_destLanguageId = null) 
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
				." WHERE NrArticle=$p_destArticleNumber ";
		if (!is_null($p_destLanguageId)) {
			$queryStr .= " AND IdLanguage=".$p_destLanguageId;
		} else {
			$queryStr .= " AND IdLanguage=".$this->m_data['IdLanguage'];
		}
		$Campsite['db']->Execute($queryStr);
	} // fn copyToExistingRecord
	
	
	/**
	 * Return an array of ArticleTypeField objects.
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
				$columnMetadata =& new ArticleTypeField($this->m_articleTypeName);
				$columnMetadata->fetch($row);
				$metadata[] =& $columnMetadata;
			}
		}
		return $metadata;
	} // fn getUserDefinedColumns

} // class ArticleData

?>