<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');

/**
 * @package Campsite
 */
class ArticleType {
	private $m_columnNames = array();
	private $m_dbTableName;

	/**
	 * Stores the article type name.
	 * @var string
	 */
	private $m_name;

	/**
	 * Stores an ArticleTypeField object
	 * @var ArticleTypeField
	 */
	private $m_metadata = null;
	private $m_dbColumns = null;
	private $m_publicFields = null;

	/**
	 * An article type is a dynamic table that is created for an article
	 * to allow different publications to display their content in different
	 * ways.
	 *
	 * @param string $p_articleType
	 */
	public function ArticleType($p_articleType)
	{
        $this->m_metadata = new ArticleTypeField($p_articleType, 'NULL');
		$this->m_name = $this->m_metadata->getArticleType();
        $this->m_dbTableName = 'X' . $this->m_name;

        if ($this->m_metadata->exists()) {
        	// Get user-defined values.
        	$this->getUserDefinedColumns();
        	foreach ($this->m_dbColumns as $columnMetaData) {
        		$this->m_columnNames[] = $columnMetaData->getName();
        	}
        } else {
        	$this->m_dbColumns = array();
        	$this->m_publicFields = array();
        }
	} // constructor


	/**
	 * Create a new Article Type.  Creates a new table in the database.
	 * @return boolean
	 */
	public function create()
	{
		global $g_ado_db;

		if (strlen($this->m_dbTableName) <= 1) {
			return false;
		}
		$queryStr = "CREATE TABLE `".$this->m_dbTableName."` (\n"
                  . "    NrArticle INT UNSIGNED NOT NULL,\n"
                  . "    IdLanguage INT UNSIGNED NOT NULL,\n"
                  . "    PRIMARY KEY(NrArticle, IdLanguage)\n"
                  . ") DEFAULT CHARSET=utf8";
		$success = $g_ado_db->Execute($queryStr);

		if ($success) {
			$metadata = new ArticleTypeField($this->getTypeName(), 'NULL');
			$success = $metadata->create(null);
		} else {
			return false;
		}

		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('The article type "$1" has been added.', $this->m_name);
	    	Log::Message($logtext, null, 61);
            CampCache::singleton()->clear('user');
		} else {
			$queryStr = "DROP TABLE `" . $this->m_dbTableName . "`";
			$result = $g_ado_db->Execute($queryStr);
		}

		return $success;
	} // fn create


	/**
	 * Return TRUE if the Article Type exists.
	 * @return boolean
	 */
	public function exists()
	{
		return $this->m_metadata->exists();
	} // fn exists


	/**
	 * Delete the article type. This will delete the entire table
	 * in the database. Not recommended unless there is no article
	 * data in the table.
	 */
	public function delete()
	{
		global $g_ado_db;

		$translation = new Translation(null, $this->m_metadata->getPhraseId());
		$translation->deletePhrase();

		foreach ($this->m_dbColumns as $field) {
			$field->delete();
		}
        $this->m_metadata->delete();

		$queryStr = "DROP TABLE `" . $this->m_dbTableName . "`";
		$success = $g_ado_db->Execute($queryStr);
		CampCache::singleton()->clear('user');
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('The article type "$1" has been deleted.', $this->m_name);
			Log::Message($logtext, null, 62);
		}
	} // fn delete


	/**
	 * Rename the article type. This will move the entire table in the
	 * database and update ArticleTypeMetadata. Usually, one wants to
	 * just rename the Display Name, which is done via SetDisplayName
	 *
	 * @param $p_newName
	 */
	public function rename($p_newName)
	{
		global $g_ado_db;

		if (!ArticleType::isValidFieldName($p_newName)) {
			return false;
		}
		$metadata = new ArticleTypeField($p_newName, 'NULL');
		if ($metadata->exists()) {
			return false;
		}

		$oldName = $this->m_name;
		$oldNameEsc = $g_ado_db->escape($this->m_name);
		$newNameEsc = $g_ado_db->escape($p_newName);

		$queryStr = "RENAME TABLE `" . $this->m_dbTableName . "` TO `X$p_newName`";
		$success = $g_ado_db->Execute($queryStr);
		if ($success) {
			$queryStr = "UPDATE ArticleTypeMetadata SET type_name = '$newNameEsc' "
			. "WHERE type_name = '$oldNameEsc'";
			$success = $g_ado_db->Execute($queryStr);
		}
        if ($success) {
            $queryStr = "UPDATE Articles SET Type = '$newNameEsc' WHERE Type = '$oldNameEsc'";
            $success = $g_ado_db->Execute($queryStr);
        }
		if ($success) {
            $this->m_name = $p_newName;
            $this->m_dbTableName = 'X'. $p_newName;

            if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logText = getGS('The article type "$1" has been renamed to "$2".', $oldName, $p_newName);
			Log::Message($logText, null, 62);
		} else {
            $queryStr = "RENAME TABLE `X$p_newName` TO `" . $this->m_dbTableName . "`";
            $g_ado_db->Execute($queryStr);
			$queryStr = "UPDATE ArticleTypeMetadata SET type_name = '$oldName' "
            . "WHERE type_name = '$newNameEsc'";
            $g_ado_db->Execute($queryStr);
            $queryStr = "UPDATE Articles SET Type = '$oldName' WHERE Type = '$newNameEsc'";
            $g_ado_db->Execute($queryStr);
		}
		return $success;
	} // fn rename


	/**
	* A quick lookup to see if the current language was already translated
	* for this article type: used by delete and update in setName.
	* Returns 0 if no translation or the phrase_id if there is one.
	*
	* @param int p_languageId
	*
	* @return 0 or phrase_id
	**/
	public function translationExists($p_languageId)
	{
		if ($this->m_metadata->getPhraseId() != -1) {
			$translation = new Translation($p_languageId, $this->m_metadata->getPhraseId());
			if ($translation->exists()) {
				return $translation->getPhraseId();
			}
		}
		return 0;
	} // fn translationExists


	/**
	 * Set the type name for the given language. A new entry in
	 * the database will be created if the language does not exist.
	 *
	 * @param int $p_languageId
	 * @param string $p_value
	 *
	 * @return boolean
	 */
	public function setName($p_languageId, $p_value)
	{
		global $g_ado_db;
		if (!is_numeric($p_languageId) || $p_languageId == 0) {
			return false;
		}

		$changed = $this->m_metadata->setName($p_languageId, $p_value);

		if ($changed) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type "$1" translation updated', $this->m_name);
			Log::Message($logtext, null, 143);
		}
		return $changed;
	} // fn setName


	/**
	 * Returns the phrase id of the AT.
	 *
	 * @return -1 or integer
	 */
	public function getPhraseId()
	{
		return $this->m_metadata->getPhraseId();
	} // fn getPhraseId


	/**
	 * Parses m_metadata for phrase_ids and returns an array of language_id => translation_text
	 *
	 * @return array
	 */
	public function getTranslations()
	{
		return Translation::GetTranslations($this->getPhraseId());
	} // fn getTranslations


	/**
	 * @return string
	 */
	public function getTypeName()
	{
		return $this->m_name;
	} // fn getTypeName


	/**
	 * @return string
	 */
	public function getTableName()
	{
		return $this->m_dbTableName;
	} // fn getTableName


	/**
	* Return an associative array of the metadata in ArticleFieldMetadata.
	*
	* @return array
	**/
	public function getMetadata()
	{
		return $this->m_metadata->getMetadata();
	} // fn getMetadata


	/**
	 * Return an array of ArticleTypeField objects.
	 *
	 * @return array
	 */
	public function getUserDefinedColumns($p_fieldName = null, $p_selectHidden = true, $p_skipCache = false)
	{
		if (is_null($p_fieldName)) {
			if ($p_skipCache || is_null($this->m_dbColumns)) {
				$this->m_dbColumns = ArticleTypeField::FetchFields(null, $this->m_name, 'NULL',
				false, false, true, true, $p_skipCache);
				$this->m_publicFields = array();
				foreach ($this->m_dbColumns as $field) {
					if (!$field->isHidden()) {
						$this->m_publicFields[] = $field;
					}
				}
			}
			return $p_selectHidden ? $this->m_dbColumns : $this->m_publicFields;
		}
		return ArticleTypeField::FetchFields($p_fieldName, $this->m_name, 'NULL',
		false, false, true, $p_selectHidden, $p_skipCache);
	} // fn getUserDefinedColumns


	/**
	 * Static function.  Returns TRUE if the given name
	 * is valid as an article type name.
	 *
	 * @param string $p_name
	 * @return boolean
	 */
	public static function IsValidFieldName($p_name)
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
	public static function GetArticleTypes($p_includeHidden = false)
	{
		global $g_ado_db;
		$queryStr = "SELECT type_name FROM ArticleTypeMetadata WHERE field_name = 'NULL'";
		if (!$p_includeHidden) {
		    $queryStr .= " AND is_hidden = 0";
		}
		$res = $g_ado_db->GetAll($queryStr);
		if (!$res) {
			return array();
		}
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
	public function setStatus($p_status)
	{
		return $this->m_metadata->setStatus($p_status);
	} // fn setStatus


	/**
	 * Returns 'hidden' if the article type should not be visible,
	 * and 'shown' if it should be.
	 *
	 * @return string (shown|hidden)
	 */
	public function getStatus()
	{
		return $this->m_metadata->getStatus();
	} // fn getStatus


	/**
	 * Return TRUE if comments are enabled for this article type.
	 *
	 * @return boolean
	 */
	public function commentsEnabled()
	{
		return $this->m_metadata->getProperty('comments_enabled');
	} // fn commentsEnabled

	/**
     * Gets the maximum size of a article type
	 * @return int
	 */
	public function getMaxSize()
	{
		return $this->m_metadata->getProperty('max_size');
	} // fn getMaxSize

	/**
	 * Return TRUE if comments are enabled for this article type.
	 *
	 * @param boolean $p_value
	 */
	public function setCommentsEnabled($p_value)
	{
		$p_value = $p_value ? '1' : '0';
		return $this->m_metadata->setProperty('comments_enabled', $p_value);
	} // fn setCommentsEnabled


	/**
	 * Gets the language code of the current translation language; or none
	 * if there is no translation.
	 *
	 * @param int p_lang
	 *
	 * @return string
	 */
	public function getDisplayNameLanguageCode($p_lang = 0)
	{
		return $this->m_metadata->getDisplayNameLanguageCode($p_lang);
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
	public function getDisplayName($p_lang = 0)
	{
		$displayName = $this->m_metadata->getDisplayName($p_lang);
		if ($displayName == 'NULL') {
			return $this->m_name;
		}
		return $displayName;
	} // fn getDisplayName


	/**
	 * Returns the number of articles associated with this type.
	 *
	 * @return int
	 **/
	public function getNumArticles()
	{
		global $g_ado_db;
        $sql = "SELECT COUNT(*) FROM `". $this->m_dbTableName ."`, Articles WHERE `"
               . $this->m_dbTableName . "`.NrArticle = Articles.Number AND `"
               . $this->m_dbTableName . "`.IdLanguage = Articles.IdLanguage";
		return $g_ado_db->GetOne($sql);
	} // fn getNumArticles


    /**
     *
     * For the preview of merge; this grabs an array of the article numbers
     * for calculating next, previous and current.
     *
     * @return array
     */
    public function getArticlesArray()
    {
        global $g_ado_db;
        $sql = "SELECT NrArticle FROM `". $this->m_dbTableName ."`, Articles WHERE `"
               . $this->m_dbTableName . "`.NrArticle = Articles.Number AND `"
               . $this->m_dbTableName . "`.IdLanguage = Articles.IdLanguage";
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
    public function __getPreviewTableName($p_table)
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


    /**
     * Returns an ArticleData object of the first found article of this type.
     * If no article was found create an empty one.
     *
     * @return object
     */
    public function getPreviewArticleData()
    {
        global $g_ado_db;
        $sql = "SELECT " . $this->m_dbTableName . ".* FROM "
               . $this->m_dbTableName .", Articles WHERE "
               . $this->m_dbTableName . ".NrArticle = Articles.Number AND "
               . $this->m_dbTableName . ".IdLanguage = Articles.IdLanguage LIMIT 1";
        $row = $g_ado_db->GetRow($sql);
        if (!$row) {
            $destArticleData = new ArticleData($this->m_name, Article::__generateArticleNumber(), 1);
        } else {
            $destArticleData = new ArticleData($this->m_name, $row['NrArticle'], $row['IdLanguage']);
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
	public function merge($p_src, $p_dest, $p_rules)
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
	    	$articleObj = new Article($row['IdLanguage'], $row['NrArticle']);
	    	$articleObj->resetCache();
	    	unset($articleObj);
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

        $sql = "DELETE FROM X$p_src";
        $g_ado_db->Execute($sql);
        CampCache::singleton()->clear('user');

	    return 1;
	} // fn merge
} // class ArticleType

?>