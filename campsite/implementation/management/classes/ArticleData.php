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
require_once($g_documentRoot.'/classes/ArticleType.php');
/**
 * @package Campsite
 */
class ArticleData extends DatabaseObject {
	var $m_columnNames = array('NrArticle', 'IdLanguage');
	var $m_keyColumnNames = array('NrArticle', 'IdLanguage');
	var $m_dbTableName;
	var $m_articleTypeName;
	var $m_articleTypeObj;
	
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
		$this->m_articleTypeObj =& new ArticleType($p_articleType);
		
		// Get user-defined values.
		//$tmp =& new ArticleType($p_articleType);
		$dbColumns = $this->articleTypeObj->getUserDefinedColumns();
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
		global $g_ado_db;
		$tmpData = $this->m_data;
		unset($tmpData['NrArticle']);
		foreach ($tmpData as $key => $data) {
			$tmpData[$key] = "'".$data."'";
		}

		$queryStr = 'INSERT IGNORE INTO '.$this->m_dbTableName
			.'(NrArticle,'.implode(',', array_keys($this->m_columnNames)).')'
			.' VALUES ('.$p_destArticleNumber.','.implode(',', $tmpData).')';
		$g_ado_db->Execute($queryStr);
	} // fn copy


	/**
	 * Copy the row in the database.
	 * @param int $p_destArticleNumber
	 * @param int $p_destLanguageId
	 * @return void
	 */
	function copyToExistingRecord($p_destArticleNumber, $p_destLanguageId = null)
	{
		global $g_ado_db;
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
		$g_ado_db->Execute($queryStr);
	} // fn copyToExistingRecord

} // class ArticleData

?>