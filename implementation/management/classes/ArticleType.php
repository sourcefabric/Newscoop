<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbColumn.php');

class ArticleType extends DatabaseObject {
	var $m_columnNames = array('NrArticle', 'IdLanguage');
	var $m_keyColumnNames = array('NrArticle', 'IdLanguage');
	var $m_dbTableName;
	var $m_columnNames = array('NrArticle', 'IdLanguage');
	
	function ArticleType($p_articleType, $p_articleId, $p_languageId) {
		$this->m_dbTableName = 'X'.$p_articleType;
		// Get user-defined values.
		$dbColumns = $this->getUserDefinedColumns();
		foreach ($dbColumns as $columnMetaData) {
			$this->m_columnNames[] = $columnMetaData->getName();
		}
		parent::DatabaseObject($this->m_columnNames);
		$this->setProperty('NrArticle', $p_articleId, false);
		$this->setProperty('IdLanguage', $p_languageId, false);
		if ($this->exists()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * Return an array of DbColumn objects.
	 *
	 * @return array
	 */
	function getUserDefinedColumns() {
		global $Campsite;
		$queryStr = 'SHOW COLUMNS FROM '.$this->m_dbTableName
					." LIKE 'F%'";
		$queryArray = $Campsite['db']->GetAll($queryStr);
		$metadata = array();
		foreach ($queryArray as $row) {
			$columnMetadata =& new DbColumn($this->m_dbTableName);
			$columnMetadata->fetch($row);
			$metadata[] =& $columnMetadata;
		}
		return $metadata;
	} // fn getUserDefinedColumns
		
} // class ArticleType

?>