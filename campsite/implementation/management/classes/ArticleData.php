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
	 * Gets the translation for a given language; default language is the
	 * session language.  If no translation is set for that language, we
	 * return the dbTableName.
     *
	 * @param int p_lang
	 *
	 * @return string
	 */
	function getDisplayName($p_lang = 0) 
	{
		if (!$p_lang) {
			$lang = camp_session_get('LoginLanguageId', 1);
		} else {
			$lang = $p_lang;
		}
		$aObj =& new ArticleType($this->m_articleTypeName);
		$translations = $aObj->getTranslations();
		if (!isset($translations[$lang])) return substr($aObj->getTableName(), 1);
		return $translations[$lang];

	} // fn getDisplayName


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
    * Return an array of ArticleTypeField objects.
    *
    * @param p_showAll boolean 
    * 
    * @return array
    */
    function getUserDefinedColumns($p_showAll = 0)
       {
			global $g_ado_db;
            if (!$p_showAll) {
                $is_hidden = " AND is_hidden=0 ";
            } else {
                $is_hidden = "";
            }
            
			$queryStr = "SELECT * FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' AND field_name != 'NULL' AND field_type IS NOT NULL $is_hidden ORDER BY field_weight ASC";
			$queryArray = $g_ado_db->GetAll($queryStr);
			$metadata = array();
			if (is_array($queryArray)) {
				foreach ($queryArray as $row) {
					$queryStr = "SHOW COLUMNS FROM ". $this->m_dbTableName ." LIKE '". $row['field_name'] ."'";
					$rowdata = $g_ado_db->GetAll($queryStr);
					$columnMetadata =& new ArticleTypeField(substr($this->m_dbTableName, 1));
					$columnMetadata->fetch($rowdata[0]);
					$columnMetadata->m_metadata = $columnMetadata->getMetadata();
					$metadata[] =& $columnMetadata;
				}
			}
			return $metadata;

       } // fn getUserDefinedColumns

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
