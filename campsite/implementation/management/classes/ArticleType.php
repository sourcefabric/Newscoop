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
			ParserCom::SendMessage('article_types', 'create', array("article_type"=>$this->m_dbTableName));
		} else {
			$queryStr = "DROP TABLE ".$this->m_dbTableName;
			$result = $g_ado_db->Execute($queryStr);
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
            $queryStr = "UPDATE Articles SET Type='". $p_newName ."' WHERE Type='". substr($this->m_dbTableName, 1) ."'";
            $success3 = $g_ado_db->Execute($queryStr);       
        }
		if ($success3) {
			$this->m_dbTableName = 'X'. $p_newName;
			if (function_exists("camp_load_language")) { camp_load_language("api"); }
			$logText = getGS('The article type $1 has been renamed to $2.', $this->m_dbTableName, $p_newName);
			Log::Message($logText, null, 62);
			ParserCom::SendMessage('article_types', 'rename', array('article_type' => $this->m_name));
		}

	} // fn rename


	/**
	* A quick lookup to see if the current language is already translated for this article type: used by delete and update in setName
	* returns 0 if no translation or the phrase_id if there is one.
	*
	* @param int p_languageId
	*
	* @return 0 or phrase_id
	**/
	function translationExists($p_languageId)
	{
		global $g_ado_db;
		$sql = "SELECT atm.*, t.* FROM ArticleTypeMetadata atm, Translations t WHERE atm.type_name='". $this->m_dbTableName ."' AND atm.field_name='NULL' AND atm.fk_phrase_id = t.phrase_id AND t.fk_language_id = '$p_languageId'";
		$row = $g_ado_db->getAll($sql);
		if (count($row)) return $row[0]['fk_phrase_id'];
		else { return 0; }

	} // fn translationExists


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
		if (!is_numeric($p_languageId) || $p_languageId == 0) {
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
			$row = $g_ado_db->GetOne($sql);

			// if this is the first translation ...
			if (!is_numeric($row)) {
				$description =& new Translation($p_languageId);
				$description->create($p_value);
				$phrase_id = $description->getPhraseId();
				// if the phrase_id isn't there, insert it.
				$sql = "UPDATE ArticleTypeMetadata SET fk_phrase_id=".$phrase_id ." WHERE type_name='". $this->m_dbTableName ."' AND field_name='NULL'";
				$changed = $g_ado_db->Execute($sql);

			} else {
				// if the phrase is already translated into atleast one language, just reuse that fk_phrase_id
				$desc =& new Translation($p_languageId, $row);
				$desc->create($p_value);
				$changed = true;

			}
		}

		if ($changed) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Type $1 updated: updated translation for ', $this->m_dbTableName);
			Log::Message($logtext, null, 143);
			ParserCom::SendMessage('article_types', 'modify', array('article_type' => $this->m_name));
		}
		return $changed;
	} // fn setName


	/**
	 * Returns the phrase id of the AT.
	 *
	 * @return -1 or integer
	 */
	function getPhraseId()
	{
		if (isset($this->m_metadata['fk_phrase_id'])) {
			return $this->m_metadata['fk_phrase_id'];
		} else {
			return -1;
		}
	} // fn getPhraseId


	/**
	 * Parses m_metadata for phrase_ids and returns an array of language_id => translation_text
	 *
	 * @return array
	 */
	function getTranslations()
	{
		$return = array();
		$tmp = Translation::getTranslations($this->getPhraseId());
		foreach ($tmp as $k => $v)
			$return[$k] = $v;
		return $return;
	} // fn getTranslations


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
	* @return array
	**/
	function getMetadata()
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' and field_name='NULL'";
		$queryArray = $g_ado_db->GetRow($queryStr);
		return $queryArray;
	} // fn getMetadata


	/**
	 * Return an array of ArticleTypeField objects.
	 *
	 * @return array
	 */
	function getUserDefinedColumns()
	{
		global $g_ado_db;
		$queryStr = "SELECT * FROM ArticleTypeMetadata "
		            ." WHERE type_name='". $this->m_dbTableName ."'"
		            ." AND field_name != 'NULL' "
		            ." AND field_type IS NOT NULL "
		            ." ORDER BY field_weight ASC";
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
	 * Static function.  Returns TRUE if the given name
	 * is valid as an article type name.
	 *
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
	 * @param boolean $p_includeHidden
	 *
	 * @return array
	 */
	function GetArticleTypes($p_includeHidden = false)
	{
		global $g_ado_db;
		$queryStr = "SELECT type_name FROM ArticleTypeMetadata WHERE field_name='NULL'";
		if (!$p_includeHidden) {
		    $queryStr .= " AND is_hidden='0'";
		}
		$res = $g_ado_db->GetAll($queryStr);
		$finalNames = array();
		foreach ($res as $v) {
			$finalNames[] = substr($v['type_name'], 1);
		}
		return $finalNames;
	} // fn GetArticleTypes


	/**
	 * Sets whether the article type should be visible
	 * or hidden.
	 *
	 * @param string p_status (hide|show)
	 */
	function setStatus($p_status)
	{
		global $g_ado_db;
		if ($p_status == 'hide') {
			$set = "is_hidden=1";
		} elseif ($p_status == 'show') {
			$set = "is_hidden=0";
		} else {
		    return;
		}
		$queryStr = "UPDATE ArticleTypeMetadata "
		            ." SET $set "
		            ." WHERE type_name='". $this->getTableName()."'"
		            ." AND field_name='NULL'";
		$ret = $g_ado_db->Execute($queryStr);
	} // fn setStatus


	/**
	 * Returns 'hidden' if the article type should not be visible,
	 * and 'shown' if it should be.
	 *
	 * @return string (shown|hidden)
	 */
	function getStatus()
	{
  		if ($this->m_metadata['is_hidden']) return 'hidden';
		else return 'shown';
	} // fn getStatus


	/**
	 * Return TRUE if comments are enabled for this article type.
	 *
	 * @return boolean
	 */
	function commentsEnabled()
	{
	    if (isset($this->m_metadata['comments_enabled'])
	        && $this->m_metadata['comments_enabled']) {
	        return true;
	    } else {
	        return false;
	    }
	} // fn commentsEnabled


	/**
	 * Return TRUE if comments are enabled for this article type.
	 *
	 * @param boolean $p_value
	 */
	function setCommentsEnabled($p_value)
	{
		global $g_ado_db;
		$p_value = $p_value ? '1' : '0';
		$queryStr = "UPDATE ArticleTypeMetadata "
		            ." SET comments_enabled=$p_value "
		            ." WHERE type_name='". $this->getTableName()."'"
		            ." AND field_name='NULL'";
		$ret = $g_ado_db->Execute($queryStr);
	} // fn setCommentsEnabled


	/**
	 * Gets the language code of the current translation language; or none
	 * if there is no translation.
	 *
	 * @param int p_lang
	 *
	 * @return string
	 */
	function getDisplayNameLanguageCode($p_lang = 0)
	{
		if (!$p_lang) {
			$lang = camp_session_get('LoginLanguageId', 1);
		} else {
			$lang = $p_lang;
		}
		$languageObj =& new Language($lang);
		$translations = $this->getTranslations();
		if (!isset($translations[$lang])) return '';
		return '('. $languageObj->getCode() .')';
	} // fn getDisplayNameLanguageCode


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

		$translations = $this->getTranslations();
		if (!isset($translations[$lang])) return substr($this->getTableName(), 1);
		return $translations[$lang];
	} // fn getDisplayName


	/**
	 * Returns the number of articles associated with this type.
	 *
	 * @return int
	 **/
	function getNumArticles()
	{
		global $g_ado_db;
		$sql = "SELECT COUNT(*) FROM ". $this->m_dbTableName;
		$res = $g_ado_db->GetOne($sql);
		return $res;
	} // fn getNumArticles

	
	/*
	 * Does the merge.  The p_rules array is associative with the key being the DESTINATION
	 * fieldname and the value being the SOURCE fieldname or '--None--'.  p_src is the table
	 * from which we are merging and p_dest is the table into which we are merging.  We assume
	 * that the p_rules array has already been verified elsewhere (cf. article_types/merge3.php).
	 * 
	 * if we are in preview mode, then I return the previewed object
	 *
	 * ? I should probably verify the p_rules in here
	 * ? What do I do if the p_src has an EXTRA column over the p_dest?  do I create that column? 
	 *
	 * The merge basically works like this.  We go through row-by-row each of the destination
	 * table's entries.  For each row, we go through each Field.  We create an insert statement
	 * that creates a new row in the src table with the value of what is in the dest field, according 
	 * to p_rules.
	 *
	 * @param p_src string
	 * @param p_dest string
	 * @param p_rules array
	 * @param p_article int
	 * @param p_preview, 'preview' or FALSE
	 *
	 * @return object ArticleType or TRUE/FALSE 
	 */
	function merge($p_src, $p_dest, $p_rules, $p_article = 0, $p_preview = false) 
	{
		global $g_ado_db;

		
		// 
		// if in preview mode:
		// first, copy over the destination table to an XPreviewNDestinationTable,
		// where N normally is 0, but on the off chance that they have a table named
		// XPreview0, I cycle through N as an integer until I get to a free table.
		//
		if ($p_preview) {
            $res = 1;
            $append = 0;
            while ($res) {    	            
				$sql = "DESC XPreview$append$p_dest";
				$res = $g_ado_db->Execute($sql);
		        $append++;     
            }
			//$sql = "CREATE TABLE XPreview$append$p_dest LIKE X$p_dest";
		    $dest = 'Preview'. $append . $p_dest;
			$previewType =& new ArticleType($dest);
	        $previewType->create();
	        $srcType =& new ArticleType($p_src);
	        $srcDbColumns = $srcType->getUserDefinedColumns(1);
	        foreach ($srcDbColumns as $dbColumn) {
	            $destATF =& new ArticleTypeField($dest, $dbColumn->getPrintName());
	            $srcATF =& new ArticleTypeField($p_src, $dbColumn->getPrintName());
	            $destATF->create($srcATF->getPrintType());   
	        }
				
		} else {	
			$dest = $p_dest;
		}

		//
		// columns come from the p_rules array
		// TODO: if there are extra columns (in src, but not in dest, I'll need to create them
		// now.
		//
		/*
		$destColumnNamesArray = array();
		foreach ($p_rules as $destColumnName => $srcColumnName) {
			array_push($destColumnNamesArray, $destColumnName);	
		}
		array_push($destColumnNamesArray, 'NrArticle');
		array_push($destColumnNamesArray, 'IdLanguage');
		$destColumnList = implode(",", $destColumnNamesArray);
         */
		// if in preview mode, we only do one article at a time
        if ($p_preview) {
            // if p_article is not set, then grab the first article
	        // otherwise, grab the selected article in p_article  	
    		if ($p_article == 0) {
	       	    $sql = "SELECT * FROM X". $p_src;	    
    		} else {
	       		$sql = "SELECT * FROM X". $p_src ." WHERE NrArticle=". $articleId;
		    }
		    $row = $g_ado_db->GetRow($sql);
		    if (!$row) {
		      return -1;    
		    }
		    $rows = array($row); // in preview mode, we only deal with one row
		    $sql = "SELECT * FROM Articles WHERE Number=". $rows[0]['NrArticle'];
		    $arow = $g_ado_db->GetRow($sql);
		    $arows = array($arow);
        } else {
            $sql = "SELECT * FROM X". $p_src;
            $rows = $g_ado_db->GetRows($sql);
        }
        
        if ($p_preview) {
            $tmpobj =& new Article();
            $obj =& new Article($rows[0]['IdLanguage'], $tmpobj->getArticleNumber());   
            $obj->create($dest, $arows[0]['Name']);         
            $objData =& $obj->getArticleData();
            foreach ($p_rules as $destColumnName => $srcColumnName) {
			    if ($srcColumnName != '--None--') {
    				$objData->setProperty($destColumnName, $rows[0]['F'.$srcColumnName]);	
	       		}
    		}
    		/*//$nextNumber = Article::__generateArticleNumber(); // ? Paul, is this the best way?
            array_push($valuesArray, $obj->getArticleNumber());
            array_push($valuesArray, $row['IdLanguage']);
	       	$insertSql .= "(". implode(',', $valuesArray) .")";
		    $g_ado_db->Execute($insertSql);
		    */
		    // insert a row in the Articles table as well
		    
        }        
/*
            $tmpOldArticle =& new Article($rows[0]['IdLanguage'], $rows[0]['NrArticle']);       
            //$oldData = $oldArticle->getArticleData(); 
            $translations = $tmpOldArticle->getTranslations();
            $oldArticle = $translations[0]; 
            $obj =& new Article($rows[0]['IdLanguage']);        
            $old_publication_id = $oldArticle->getPublicationId();
            $old_name = $oldArticle->getName();
            $old_issue_number = $oldArticle->getIssueNumber();
            $old_section_number = $oldArticle->getSectionNumber();
            $obj->create($dest, $old_name, $old_publication_id, $old_issue_number, $old_section_number);
          		    // Insert an entry into the article type table.
	//	    $articleData =& new ArticleData($this->m_data['Type'],
	//		    $this->m_data['Number'],
	//		    $this->m_data['IdLanguage']);
	//	    $articleData->create();
            //$objData = $obj->getArticleData();
            //foreach ($p_rules as $destColumnName => $srcColumnName) {
             //   if ($srcColumnName != '--None--') {
            //        $objData->setProperty($destColumnName, $oldData->getProperty('F'. $srcColumnName));     
            //    }
           // }      
        }
*/
        if ($p_preview) {                        
            //ql = "DROP TABLE X$dest";
            //$g_ado_db->Execute($sql);
            return $obj; 
      	} else { return true; }
	} // fn merge 
	
} // class ArticleType

?>