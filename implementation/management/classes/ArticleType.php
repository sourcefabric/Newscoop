<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DatabaseObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/DbColumn.php");

class ArticleType extends DatabaseObject {
	var $m_columnNames = array("NrArticle", "IdLanguage");
	var $m_primaryKeyColumnNames = array("NrArticle", "IdLanguage");
	var $m_dbTableName;
	var $NrArticle;
	var $IdLanguage;
	
	function ArticleType($p_articleType, $p_articleId, $p_languageId) {
		$this->m_dbTableName = "X".$p_articleType;
		$this->NrArticle = $p_articleId;
		$this->IdLanguage = $p_languageId;
		// Get user-defined values.
		$dbColumns = $this->getUserDefinedColumns();
		foreach ($dbColumns as $dbColumn) {
			$columnName = $dbColumn->getName();
			$this->$columnName = null;
			array_push($this->m_columnNames, $columnName);
		}
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
		$queryStr = "SHOW COLUMNS FROM ".$this->m_dbTableName
					." LIKE 'F%'";
		$queryArray = $Campsite["db"]->GetAll($queryStr);
		$metadata = array();
		foreach ($queryArray as $row) {
			$columnMetadata =& new DbColumn($this->m_dbTableName);
			$columnMetadata->fetch($row);
			$metadata[] =& $columnMetadata;
		}
		return $metadata;
	} // fn getUserDefinedColumns
	

	function getColumnValue($p_columnName) {
		return $this->$p_columnName;
	} // fn getColumnValue
	
	
	/**
	 *
	 * @param boolean p_refetch
	 *		Set this to true if you are including SQL commands in the column value.
	 *		This will cause a refetch of the row since there is no way to determine
	 *		what value the column was set to. 
	 */
	function setColumnValue($p_columnName, $p_columnValue, $p_refetch = false) {
		global $Campsite;
		$queryStr = "UPDATE ".$this->m_dbTableName
					." SET ".$p_columnName."=".$p_columnValue
					." WHERE ".$this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
		if ($p_refetch) {
			$this->fetch();
		}
		else {
			$this->$p_columnName = $p_columnValue;
		}
	} // fn setColumnValue
	
	
	/**
	 * Set the body of the article.
	 *
	 * @param string body
	 * 
	 * @return void
	 */
	function setBody($body) {
		$queryStr = "UPDATE ".$this->getArticleTypeTableName()
					." SET Fbody='".addslashes(trim($body))."'"
					." WHERE NrArticle='".$this->m_number."'"
					." AND IdLanguage='".$this->m_language."'";
		mysql_query($queryStr);
		$this->m_body = trim($body);
	} // fn setBody

	
	/**
	 * Set the intro of the article.
	 *
	 * @param string into
	 *
	 * @return void
	 */
	function setIntro($intro) {
		parent::setProperty("Fintro", "m_intro", $p_intro);
		$queryStr = "UPDATE ".$this->getArticleTypeTableName()
					." SET Fintro='".addslashes(trim($intro))."'"
					." WHERE NrArticle='".$this->m_number."'"
					." AND IdLanguage='".$this->m_language."'";
		mysql_query($queryStr);
		$this->m_intro = trim($intro);
	} // fn setIntro	
	
} // class ArticleType

?>