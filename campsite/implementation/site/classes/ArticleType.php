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
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/ArticleTypeField.php');
require_once($g_documentRoot.'/classes/ParserCom.php');
require_once($g_documentRoot.'/classes/Translation.php');
require_once($g_documentRoot.'/'.$ADMIN_DIR.'/localizer/Localizer.php');

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
						."VALUES ('".$this->m_name."', 'NULL')";
			$success2 = $g_ado_db->Execute($queryStr);
		} else {
			return $success;
		}

		if ($success2) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
		    $logtext = getGS('The article type $1 has been added.', $this->m_name);
	    	Log::Message($logtext, null, 61);
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
		$queryStr = "SHOW TABLES LIKE '".$this->m_dbTableName."'";
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
			$queryStr = "DELETE FROM Translations WHERE phrase_id = '" . $this->m_metadata['fk_phrase_id'] . "'";
			$g_ado_db->Execute($queryStr);
			$queryStr = "DELETE FROM ArticleTypeMetadata WHERE type_name='".$this->m_name."'";
			$success2 = $g_ado_db->Execute($queryStr);
		}

		if ($success2) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('The article type $1 has been deleted.', $this->m_name);
			Log::Message($logtext, null, 62);
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
			$queryStr = "UPDATE ArticleTypeMetadata SET type_name='$p_newName' WHERE type_name='". $this->m_name ."'";
			$success2 = $g_ado_db->Execute($queryStr);
		}
        if ($success2) {
            $queryStr = "UPDATE Articles SET Type='". $p_newName ."' WHERE Type='". $this->m_name ."'";
            $success3 = $g_ado_db->Execute($queryStr);
        }
		if ($success3) {
			$this->m_name = $p_newName;
			$this->m_dbTableName = 'X'. $p_newName;
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logText = getGS('The article type $1 has been renamed to $2.', $this->m_name, $p_newName);
			Log::Message($logText, null, 62);
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
		$sql = "SELECT atm.*, t.* FROM ArticleTypeMetadata atm, Translations t WHERE atm.type_name='". $this->m_name ."' AND atm.field_name='NULL' AND atm.fk_phrase_id = t.phrase_id AND t.fk_language_id = '$p_languageId'";
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
			    $this->m_metadata['fk_phrase_id'] = null;
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
			$sql = "SELECT fk_phrase_id FROM ArticleTypeMetadata WHERE type_name='". $this->m_name ."' AND field_name='NULL'";
			$row = $g_ado_db->GetOne($sql);

			// if this is the first translation ...
			if (!is_numeric($row)) {
				$description =& new Translation($p_languageId);
				$description->create($p_value);
				$phrase_id = $description->getPhraseId();
				// if the phrase_id isn't there, insert it.
				$sql = "UPDATE ArticleTypeMetadata SET fk_phrase_id=".$phrase_id ." WHERE type_name='". $this->m_name ."' AND field_name='NULL'";
				$changed = $g_ado_db->Execute($sql);
				if ($changed) {
					$this->m_metadata['fk_phrase_id'] = $phrase_id;
				}
			} else {
				// if the phrase is already translated into atleast one language, just reuse that fk_phrase_id
				$desc =& new Translation($p_languageId, $row);
				$desc->create($p_value);
				$changed = true;
			}
		}

		if ($changed) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Article Type $1 translation updated', $this->m_name);
			Log::Message($logtext, null, 143);
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
	function getTypeName()
	{
		return $this->m_name;
	} // fn getTypeName


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
		$queryStr = "SELECT * FROM ArticleTypeMetadata WHERE type_name='". $this->m_name ."' and field_name='NULL'";
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
		            ." WHERE type_name='". $this->m_name ."'"
		            ." AND field_name != 'NULL' "
		            ." AND field_type IS NOT NULL "
		            ." AND type_name NOT LIKE 'XPreview%'"
		            ." ORDER BY field_weight ASC";
		$queryArray = $g_ado_db->GetAll($queryStr);
		$metadata = array();
		if (is_array($queryArray)) {
			foreach ($queryArray as $row) {
				$queryStr = "SHOW COLUMNS FROM ". $this->m_dbTableName ." LIKE 'F". $row['field_name'] ."'";
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
		$hasLetter = false;
		for ($i = 0; $i < strlen($p_name); $i++) {
			$c = $p_name[$i];
			$isLetter = ($c >= 'A' && $c <= 'Z') || ($c >= 'a' && $c <= 'z');
			$hasLetter = $hasLetter || $isLetter;
			$valid = $isLetter || $c == '_';
			if (!$valid) {
			  return false;
			}
		}
		return true && $hasLetter;
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
		    $queryStr .= " AND is_hidden=0";
		}
		$res = $g_ado_db->GetAll($queryStr);
		if (!$res) return array();
		$finalNames = array();
		foreach ($res as $v) {
			$finalNames[] = $v['type_name'];
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
			$status = 1;
		} elseif ($p_status == 'show') {
			$status = 0;
		} else {
		    return;
		}
		$queryStr = "UPDATE ArticleTypeMetadata "
		            ." SET is_hidden = $status "
		            ." WHERE type_name='". $this->m_name."'"
		            ." AND field_name='NULL'";
		$ret = $g_ado_db->Execute($queryStr);
		if ($ret) {
			$this->m_metadata['is_hidden'] = $status;
		}
		return $ret;
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
		            ." WHERE type_name='". $this->m_name ."'"
		            ." AND field_name='NULL'";
		$ret = $g_ado_db->Execute($queryStr);
		if ($ret) {
			$this->m_metadata['comments_enabled'] = $p_value;
		}
		return $ret;
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

    /**
     *
     * For the preview of merge; this grabs an array of the article numbers for calculating next, prev, and cur
     *
     * @return array
     */
    function getArticlesArray()
    {
        global $g_ado_db;
        $sql = "SELECT NrArticle FROM ". $this->m_dbTableName;
        $rows = $g_ado_db->GetAll($sql);
        $returnArray = array();
        foreach ($rows as $row) {
            $returnArray[] = $row['NrArticle'];
        }
        return $returnArray;
    }


    /**
     * Creates a preview table of PreviewNTablename, where N is a unique integer.
     *
     * @param string p_table
     * @return string a tablename
     */
    function __getPreviewTableName($p_table)
    {
        $res = 1;
        $append = 0;
        while ($res) {
    		$append++;
            $sql = "DESC XPreview$append$p_table";
    		$res = $g_ado_db->GetOne($sql);
        }
        $dest = 'Preview'. $append . $p_table;
        $sql = "CREATE TABLE X$dest LIKE X$p_table";
        $res = $g_ado_db->Execute($sql);
        if (!$res) return 0;
        $sql = "SELECT * FROM ArticleTypeMetadata WHERE type_name='$p_table'";
        $rows = $g_ado_db->GetAll($sql);
        if (!count($rows)) return 0;
        foreach ($rows as $row) {
            $keys = array();
            $values = array();
            foreach ($row as $k => $v) {
                $keys[] = $k;
                if ($k == 'type_name') $v = $dest;
                if (!is_numeric($v)) $values[] = "'$v'";
                else $values[] = $v;
            }
            $keysString = implode(',', $keys);
            $valuesString = implode(',', $values);
            $sql = "INSERT INTO ArticleTypeMetadata ($keysString) VALUES ($valuesString)";
            $res = $g_ado_db->Execute($sql);
            if (!$res) return 0;
        }
        return 'Preview'. $dest;
    } // fn __getPreviewTableName

    function getPreviewArticleData() {
        global $g_ado_db;
        $sql = "SELECT * FROM ". $this->m_dbTableName ." LIMIT 1";
        $row = $g_ado_db->GetRow($sql);
        if (!$row) {
            $destArticleData =& new ArticleData($this->m_name, Article::__generateArticleNumber(), 1);
        } else {
            $destArticleData =& new ArticleData($this->m_name, $row['NrArticle'], $row['IdLanguage']);
        }
        return $destArticleData;
    }
	/**
     * Does the merge or a preview of the merge.
     * The p_rules array is an associative array with the key being the DESTINATION fieldname
     * and the values being the SOURCE fieldname (without Fs).
     * E.g.
     * $p_rules = array('a' => 'a', 'b' => 'title', 'd' => 'body');
     *
     * p_rules is verified elsewhere (see article_types/merge3.php).
     *
     * If we are doing an actual merge, all that happens is that we rename the Type in Articles
     * from SrcType to DestType and run the merge (p_rules) on the XSrcTable entries and move
     * them over to XDestType.  Merged articles have the same ArticleNumber as their originals.
     *
	 * @param string p_src
	 * @param string p_dest
	 * @param array p_rules
	 *
	 * @return boolean
	 **/
	function merge($p_src, $p_dest, $p_rules)
	{
		global $g_ado_db;
	    // non-preview mode, the actual merge
        // all that needs to be done is to copy entries from Xsrc to Xdest
        // and then reassign the type in the Articles table
	    $sql = "SELECT * FROM X$p_src";
	    $rows = $g_ado_db->GetAll($sql);
	    if (!count($rows)) {
	    	return 0;
	    }
	    foreach ($rows as $row) {
            $fields = array();
            $values = array();
            foreach ($p_rules as $destC => $srcC) {
                $fields[] = 'F'. $destC;
                if ($srcC == 'NULL') $values[] = "''";
                else if (is_numeric($row['F'. $srcC])) $values[] = $row['F'. $srcC];
                else $values[] = "'". $g_ado_db->escape($row['F'. $srcC]) ."'";
            }
            $fields[] = 'NrArticle';
            $values[] = $row['NrArticle'];
            $fields[] = 'IdLanguage';
            $values[] = $row['IdLanguage'];
            $fieldsString = implode(',', $fields);
            $valuesString = implode(',', $values);
            $sql = "INSERT IGNORE INTO X$p_dest ($fieldsString) VALUES ($valuesString)";
		    if (!$g_ado_db->Execute($sql)) {
		    	return 0;
		    }
	    }

        $sql = "UPDATE Articles SET Type='$p_dest' WHERE Type='$p_src'";
        if (!$g_ado_db->Execute($sql)) {
        	return 0;
        }

	    return 1;
	} // fn merge
} // class ArticleType

?>