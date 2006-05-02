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
require_once($g_documentRoot.'/classes/Translation.php');

/**
 * @package Campsite
 */
class ArticleType {
	var $m_columnNames = array();
	var $m_dbTableName;
	var $m_name;
	var $m_metadata;
	var $m_dbColumns;

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
		$this->m_dbColumns = $this->getUserDefinedColumns();
		foreach ($this->m_dbColumns as $columnMetaData) {
			$this->m_columnNames[] = $columnMetaData->getName();
		}
		$this->m_metadata = $this->getMetadata();
	} // constructor


	/**
	 * Create a new Article Type.  Creates a new table in the database.
	 * @return boolean
	 */
	function create()
	{
		global $g_ado_db;
		$queryStr = "CREATE TABLE `".$this->m_dbTableName."`"
					."(NrArticle INT UNSIGNED NOT NULL, "
					." IdLanguage INT UNSIGNED NOT NULL, "
					." PRIMARY KEY(NrArticle, IdLanguage))";
		$success = $g_ado_db->Execute($queryStr);

		if ($success) {
			$queryStr = "INSERT INTO ArticleTypeMetadata"
						."(type_name, field_name) "
						."VALUES ('".$this->m_dbTableName."', 'NULL')";
			$success2 = $g_ado_db->Execute($queryStr);
		} else {
			return $success;
		}

		if ($success2) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
		    $logtext = getGS('The article type $1 has been added.', $this->m_dbTableName);
	    	Log::Message($logtext, null, 61);
			//ParserCom::SendMessage('article_types', 'create', array("article_type"=>$this->m_dbTableName));
		} else {
			$queryStr = "DROP TABLE ".$this->m_dbTableName;
			$result = $g_ado_db->Execute($queryStr);
			// RFC: Maybe a check on this result as well?  We drop the table since creation is two-tier: create the table,
			// then add the entry into ArticleTypeMetadata; so if the second part failed, but hte first part worked (when would
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
		global $g_ado_db;
		$queryStr = "SHOW TABLES LIKE '".$this->m_dbTableName."'"; // the old code had an X, but m_dbTableName in ArticleType::articleType() is already with an X pjh
		$result = $g_ado_db->GetOne($queryStr);
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
		global $g_ado_db;
		$queryStr = "DROP TABLE ".$this->m_dbTableName;
		$success = $g_ado_db->Execute($queryStr);
		if ($success) {
			$queryStr = "DELETE FROM ArticleTypeMetadata WHERE type_name='".$this->m_dbTableName."'";
			$success2 = $g_ado_db->Execute($queryStr);
		}

		if ($success2) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('The article type $1 has been deleted.', $this->m_dbTableName);
			Log::Message($logtext, null, 62);
			ParserCom::SendMessage('article_types', 'delete', array("article_type" => $this->m_name));
		}
	} // fn delete

	/**
	 * Rename the article type.  This will move the entire table in the database and update ArticleTypeMetadata.
	 * Usually, one wants to just rename the Display Name, which is done via SetDisplayName
	 *
	 */
	function rename($p_newName)
	{
		global $g_ado_db;
		if (!ArticleType::isValidFieldName($p_newName)) return 0;
		$queryStr = "RENAME TABLE ".$this->m_dbTableName ." TO X".$p_newName;
		$success = $g_ado_db->Execute($queryStr);
		if ($success) {
			$queryStr = "UPDATE ArticleTypeMetadata SET type_name='X". $p_newName ."' WHERE type_name='". $this->m_dbTableName ."'";
			$success2 = $g_ado_db->Execute($queryStr);
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
	* quick lookup to see if the current language is already translated for this article type: used by delete and update in setName
	* returns 0 if no translation or the phrase_id if there is one.
	**/
	function translationExists($p_languageId) {
		global $g_ado_db;
		$sql = "SELECT atm.*, t.* FROM ArticleTypeMetadata atm, Translations t WHERE atm.type_name='". $this->m_dbTableName ."' AND atm.fk_phrase_id = t.phrase_id AND t.fk_language_id = '$p_languageId'"; 		
		$row = $g_ado_db->getAll($sql);
		if (count($row)) return $row[0]['fk_phrase_id'];
		else { return 0; }
	}

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
		global $g_ado_db;
		if (!is_numeric($p_languageId)) {
			return false;
		}
		// if the string is empty, nuke it		
		if (!is_string($p_value) || $p_value == '') {
			if ($phrase_id = $this->translationExists($p_languageId)) {
			    $trans =& new Translation($p_languageId, $phrase_id);
			    $trans->delete();
				$changed = true;
			} else { $changed = false; }
		} else if ($phrase_id = $this->translationExists($p_languageId)) {
			// just update
			$description =& new Translation($p_languageId, $phrase_id);
			$description->setText($p_value);
			$changed = true;
		} else {
			// Insert the new translation.
			// first get the fk_phrase_id 
			$sql = "SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' AND field_name='NULL'";
			$row = $g_ado_db->GetRow($sql);
			// if this is the first translation ...
			if (!is_numeric($row['fk_phrase_id'])) {
				$description =& new Translation($p_languageId);
				$description->create($p_value);
				$phrase_id = $description->getPhraseId();
				// if the phrase_id isn't there, insert it.
				$sql = "UPDATE ArticleTypeMetadata SET fk_phrase_id=".$phrase_id ." WHERE type_name='". $this->m_dbTableName ."' AND field_name='NULL'";
				$changed = $g_ado_db->Execute($sql);			
			} else { 
				// if the phrase is already translated into atleast one language, just reuse that fk_phrase_id
				$desc =& new Translation($p_languageId, $row['fk_phrase_id']);
				$desc->create($p_value);
				$changed = true; 
			}
		}

		if ($changed) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Type $1 updated', $this->m_dbTableName.": (".$oldValue. " -> ".$p_value .")");
			Log::Message($logtext, null, 143);		
			//ParserCom::SendMessage('article_types', 'modify', array('article_type' => $this->m_name));
		}
		return $changed;
	} // fn setName
	function getPhraseId() {
		if (isset($this->m_metadata[0]['fk_phrase_id'])) 
			return $this->m_metadata[0]['fk_phrase_id'];
		else
			return -1;
	}
	/**
	 * Parses m_metadata for phrase_ids and returns an array of language_id => translation_text
	 *
	 * @return array
	 *
	 */
	function getTranslations() {
		$return = array();
		$tmp = Translation::getTranslations($this->getPhraseId());
		foreach ($tmp as $k => $v)
			$return[$k] = $v;
		return $return;
	}

	/**
	 * @return string
	 */
	function getTableName()
	{
		return $this->m_dbTableName;
	} // fn getTableName


	/**
	* Return an associative array of the metadata in ArticleFieldMetadata.
	*
	**/
	function getMetadata() {
		global $g_ado_db;
		$queryStr = "SELECT * FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' and field_name='NULL'";
		$queryArray = $g_ado_db->GetAll($queryStr);
		return $queryArray;
	}

	/**
	 * Return an array of ArticleTypeField objects.
	 *
	 * @return array
	 */
	function getUserDefinedColumns()
	{
		global $g_ado_db;
		#$queryStr = 'SHOW COLUMNS FROM '.$this->m_dbTableName
		#			." LIKE 'F%'";
		$queryStr = "SELECT * FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' AND field_name != 'NULL' AND field_type IS NOT NULL ORDER BY field_weight DESC";
		$queryArray = $g_ado_db->GetAll($queryStr);
		$metadata = array();
		if (is_array($queryArray)) {
			foreach ($queryArray as $row) {
				$queryStr = "SHOW COLUMNS FROM ". $this->m_dbTableName ." LIKE '". $row['field_name'] ."'";
				$rowdata = $g_ado_db->GetAll($queryStr);
				$columnMetadata =& new ArticleTypeField($this->m_name);
				$columnMetadata->fetch($rowdata[0]);
				$columnMetadata->m_metadata = $columnMetadata->getMetadata();
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
		global $g_ado_db;
		$queryStr = "SELECT type_name FROM ArticleTypeMetadata WHERE field_name='NULL'";
		$res = $g_ado_db->GetAll($queryStr);
		$finalNames = array();
		foreach ($res as $v) {
			$finalNames[] = substr($v['type_name'], 1);
		}
		return $finalNames;
	} // fn GetArticleTypes

	/**
	 * sets the is_hidden variable
	 */
	function setStatus($p_status) {
		global $g_ado_db;
		if ($p_status == 'hide')
			$set = "is_hidden=1";
		if ($p_status == 'show')
			$set = "is_hidden=0";
		$queryStr = "UPDATE ArticleTypeMetadata SET $set WHERE type_name='". $this->getTableName() ."' AND field_name='NULL'";
		$ret = $g_ado_db->Execute($queryStr);
	}

	/*
	* returns 'shown' or 'hidden'
	*/
	function getStatus() {
  		if ($this->m_metadata[0]['is_hidden']) return 'hidden';
		else return 'shown';
	}

	/**
	*
	* gets the display name of a type; this is based on the native language -- and if no native language translation is available
	* we use dbTableName
	*
	**/
	function getDisplayName($p_langBrackets = 1) {
		global $_REQUEST;
		$loginLanguageId = 0;
		$loginLanguage = Language::GetLanguages(null, $_REQUEST['TOL_Language']);
		if (is_array($loginLanguage)) {
			$loginLanguage = array_pop($loginLanguage);
			$loginLanguageId = $loginLanguage->getLanguageId();
		}
		$translations = $this->getTranslations();
		if (!isset($translations[$loginLanguageId])) return substr($this->getTableName(), 1);
		if ($p_langBrackets) return $translations[$loginLanguageId] .' ('. $loginLanguage->getCode() .')';
		return $translations[$loginLanguageId];

	}
	
	/*
	* returns the number of articles associated with this type.
	*
	**/
	function getNumArticles() {
		global $g_ado_db;
		$sql = "SELECT COUNT(*) FROM ". $this->m_dbTableName; 
		$res = $g_ado_db->GetOne($sql);
		return $res;
	}

} // class ArticleType

?>