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
			$queryStr = "INSERT INTO `data_type_fields`"
						."(table_name, field_name, weight, is_hidden, fk_phrase_id) "
						."VALUES ('".$this->m_dbTableName."', NULL, 0, 1, NULL)";
			$success2 = $Campsite['db']->Execute($queryStr);			
		} else {
			return $success;
		}

		if ($success2) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
		    $logtext = getGS('The article type $1 has been added.', $this->m_dbTableName);
	    	Log::Message($logtext, null, 61);
			ParserCom::SendMessage('article_types', 'create', array("article_type"=>$cName));
		} else {
			$queryStr = "DROP TABLE ".$this->m_dbTableName;
			$result = $Campsite['db']->Execute($queryStr);
			// RFC: Maybe a check on this result as well?  We drop the table since creation is two-tier: create the table,
			// then add the entry into data_type_fields; so if the second part failed, but hte first part worked (when would 
			// that ever really happen??) we drop the table and return 0.  But if the table drop breaks too, should I
			// give a more verbose error.  I'm voting not, due to rarity--and if things get that bad they have other issues.
		}
		
		return $success2;
	} // fn create


	/**
	 * Return TRUE if the Article Type exists.
	 * @return boolean
	 */
	function exists()
	{
		global $Campsite;
		$queryStr = "SHOW TABLES LIKE '".$this->m_dbTableName."'"; // the old code had an X, but m_dbTableName in ArticleType::articleType() is already with an X pjh
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
			$queryStr = "DELETE FROM data_type_fields WHERE table_name='".$this->m_dbTableName."'";
			$success2 = $Campsite['db']->Execute($queryStr);
		} 
		
		if ($success2) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('The article type $1 has been deleted.', $this->m_dbTableName);
			Log::Message($logtext, null, 62);
			ParserCom::SendMessage('article_types', 'delete', array("article_type" => $this->m_name));
		}
	} // fn delete

	/**
	 * Rename the article type.  This will move the entire table in the database and update data_type_fields.
	 * Usually, one wants to just rename the Display Name, which is done via SetDisplayName
	 *
	 */
	function rename($p_newName)
	{
		global $Campsite;
		if (!ArticleType::isValidFieldName($p_newName)) return 0;
		$queryStr = "RENAME TABLE ".$this->m_dbTableName ." TO X".$p_newName;
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			$queryStr = "UPDATE data_type_fields SET table_name='X". $p_newName ."' WHERE table_name='". $this->m_dbTableName ."'";
			$success2 = $Campsite['db']->Execute($queryStr);		
		}


		if ($success2) {
			$this->m_dbTableName = 'X'. $p_newName;
			if (function_exists("camp_load_language")) { camp_load_language("api"); }
			$logText = getGS('The article type $1 has been renamed to $2.', $this->m_dbTableName, $p_newName);
			Log::Message($logText, null, 62);
			ParserCom::SendMessage('article_types', 'rename', array('article_type' => $this->m_name));
		}
	
			
	}

	
	/**
	 * @return string
	 */
	function getName($p_languageId) 
	{
		if (is_numeric($p_languageId) && isset($this->m_names[$p_languageId])) {
			return $this->m_names[$p_languageId];;
		} else {
			return "";
		}
	} // fn getName
	
	
	/**
	 * Set the type name for the given language.  A new entry in 
	 * the database will be created if the language does not exist.
	 * 
	 * @param int $p_languageId
	 * @param string $p_value
	 * 
	 * @return boolean
	 */
	function setName($p_languageId, $p_value) 
	{
		global $Campsite;
		if (!is_numeric($p_languageId)) {
			return false;
		}
		
		
		// if the string is empty, nuke it		
		if (!is_string($p_value)) {
			$sql = "DELETE FROM data_type_fields WHERE table_name=". $this->m_dbTableName ." AND fk_phrase_id=". $p_languageId;
			$changed = $Campsite['db']->Execute($sql);
		}
		
		if (isset($this->m_names[$p_languageId])) {
			// Update the name.
			$oldValue = $this->m_names[$p_languageId];
			$sql = "UPDATE data_type_fields SET table_name='".mysql_real_escape_string($p_value)."' "
					." WHERE table_name=".$this->m_dbTableName
					." AND fk_phrase_id=".$p_languageId;
			$changed = $Campsite['db']->Execute($sql);
		} else {
			// Insert the new translation.
			$oldValue = "";
			$sql = "INSERT INTO data_type_fields SET table_name='".mysql_real_escape_string($p_value)."' "
					.", fk_phrase_id=".$p_languageId;
			$changed = $Campsite['db']->Execute($sql);			
		}
		if ($changed) {
			$this->m_names[$p_languageId] = $p_value;
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Type $1 updated', $this->m_dbTableName.": (".$oldValue. " -> ".$this->m_names[$p_languageId].")");
			Log::Message($logtext, null, 143);		
			//ParserCom::SendMessage('article_types', 'modify', array('article_type' => $this->m_name));
		}
		return $changed;
	} // fn setName
	

	/**
	 * Get all translations of the topic in an array indexed by 
	 * the language ID.
	 * 
	 * @return array
	 */
	function getTranslations() 
	{
	    return $this->m_names;
	} // fn getTranslations
	
	
	/**
	 * Return the number of translations of this topic. 
	 * 
	 * @return int
	 */
	function getNumTranslations()
	{
		return count($this->m_names);
	} // fn getNumTranslations
	
	 

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