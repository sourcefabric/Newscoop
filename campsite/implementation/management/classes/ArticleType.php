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
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/ArticleTypeField.php');
require_once($g_documentRoot.'/classes/ParserCom.php');

/**
 * @package Campsite
 */
class ArticleType {
	var $m_columnNames = array();
	var $m_dbTableName;
	var $m_name;


	/**
	 * An article type is a dynamic table that is created for an article
	 * to allow different publications to display their content in different
	 * ways.
	 *
	 * @param string $p_articleType
	 */
	function ArticleType($p_articleType)
	{
		$this->m_name = $p_articleType;
		$this->m_dbTableName = 'X'.$p_articleType;
		// Get user-defined values.
		$dbColumns = $this->getUserDefinedColumns();
		foreach ($dbColumns as $columnMetaData) {
			$this->m_columnNames[] = $columnMetaData->getName();
		}
	} // constructor


	/**
	 * Create a new Article Type.  Creates a new table in the database.
	 * @return boolean
	 */
	function create()
	{
		global $Campsite;
		$queryStr = "CREATE TABLE `".$this->m_dbTableName."`"
					."(NrArticle INT UNSIGNED NOT NULL, "
					." IdLanguage INT UNSIGNED NOT NULL, "
					." PRIMARY KEY(NrArticle, IdLanguage))";
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
		    $logtext = getGS('The article type $1 has been added.', $this->m_dbTableName);
	    	Log::Message($logtext, null, 61);
			ParserCom::SendMessage('article_types', 'create', array("article_type"=>$cName));
		}
		return $success;
	} // fn create


	/**
	 * Return TRUE if the Article Type exists.
	 * @return boolean
	 */
	function exists()
	{
		global $Campsite;
		$queryStr = "SHOW TABLES LIKE 'X".$this->m_dbTableName."'";
		$result = $Campsite['db']->GetOne($queryStr);
		if ($result) {
			return true;
		} else {
			return false;
		}
	} // fn exists


	/**
	 * Delete the article type.  This will delete the entire table
	 * in the database.  Not recommended unless there is no article
	 * data in the table.
	 */
	function delete()
	{
		global $Campsite;
		$queryStr = "DROP TABLE ".$this->m_dbTableName;
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('The article type $1 has been deleted.', $this->m_dbTableName);
			Log::Message($logtext, null, 62);
			ParserCom::SendMessage('article_types', 'delete', array("article_type" => $this->m_name));
		}
	} // fn delete


	/**
	 * @return string
	 */
	function getTableName()
	{
		return $this->m_dbTableName;
	} // fn getTableName


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
				$columnMetadata =& new ArticleTypeField($this->m_name);
				$columnMetadata->fetch($row);
				$metadata[] =& $columnMetadata;
			}
		}
		return $metadata;
	} // fn getUserDefinedColumns


	/**
	 * Static function.
	 * @param string $p_name
	 * @return boolean
	 */
	function IsValidFieldName($p_name)
	{
		if (empty($p_name)) {
			return false;
		}
		for ($i = 0; $i < strlen($p_name); $i++) {
			$c = $p_name[$i];
			$valid = ($c >= 'A' && $c <= 'Z') || ($c >= 'a' && $c <= 'z') || $c == '_';
			if (!$valid) {
			  return false;
			}
		}
		return true;
	} // fn IsValidFieldName


	/**
	 * Get all article types that currently exist.
	 * Returns an array of strings.
	 *
	 * @return array
	 */
	function GetArticleTypes()
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
	} // fn GetArticleTypes

} // class ArticleType

?>