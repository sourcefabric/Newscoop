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

require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Topic.php');

/**
 * @package Campsite
 */
class ArticleTypeField {
	var $m_dbTableName;
	var $m_articleTypeName;
	var $m_dbColumnName;
	var $m_fieldName;
	var $Field;
	var $Type;
	var $Null;
	var $Key;
	var $Default;
	var $Extra;
	var $m_metadata;

	public function ArticleTypeField($p_articleTypeName = null, $p_fieldName = null)
	{
		$this->m_articleTypeName = $p_articleTypeName;

		$this->m_dbTableName = "X".$p_articleTypeName;
		$this->m_dbColumnName = "F".$p_fieldName;
		$this->m_fieldName = $p_fieldName;
		if (!is_null($this->m_articleTypeName) && !is_null($this->m_fieldName)) {
			$this->fetch();
			$this->m_metadata = $this->getMetadata();
		}
	} // constructor


	/**
	 * @return string
	 */
	public function getDbTableName()
	{
		return $this->m_dbTableName;
	} // fn getDbTableName


	/**
	 * Returns the article type name.
	 *
	 * @return string
	 */
	public function getArticleType()
	{
		return substr($this->m_dbTableName, 1);
	} // fn getArticleType


	/**
	 * Rename the article type.  This will move the entire table in the database and update ArticleTypeMetadata.
	 * Usually, one wants to just rename the Display Name, which is done via SetDisplayName
	 *
	 * @param string p_newName
	 *
	 */
	public function rename($p_newName)
	{
		global $g_ado_db;
		if (!ArticleType::isValidFieldName($p_newName)) return 0;
		// TODO: This sql sequence could be cleaned up for efficiency.  Renaming columns is tricky in mysql. pjh 2006/March
		$queryStr = "SHOW COLUMNS FROM ". $this->m_dbTableName;
		$success = 0;
		$res = $g_ado_db->getAll($queryStr);
		if (empty($res))
			return;

		$queryStr = 0;

	    if (count($res) > 0) {
	    	foreach ($res as $row) {
	    		if ($row['Field'] == $this->m_dbColumnName) {
					$queryStr = "ALTER TABLE ". $this->m_dbTableName ." CHANGE COLUMN ". $this->m_dbColumnName ." F". $p_newName ." ". $row['Type'];
					break;
	    		}
	    	}
		}
		if ($queryStr) {
			$success = $g_ado_db->Execute($queryStr);
		}

		if ($success) {
			$queryStr = "UPDATE ArticleTypeMetadata SET field_name='". $p_newName ."' WHERE field_name='". $this->m_fieldName ."' AND type_name='". $this->m_articleTypeName ."'";
			$success2 = $g_ado_db->Execute($queryStr);
		}


		if ($success2) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logText = getGS('The article type field $1 has been renamed to $2.', $this->m_fieldName, $p_newName);
    		$this->m_dbColumnName = 'F'. $p_newName;
			$this->m_fieldName = $p_newName;
			Log::Message($logText, null, 62);
		}
	} // fn rename


	/**
	 * Create a column in the table.
	 * @param string $p_type
	 *		Can be one of: 'text', 'date', 'body'.
	 */
	public function create($p_type, $p_rootTopicId = 0, $p_isContent = false)
	{
		global $g_ado_db;

		$isContent = 0;
		$p_type = strtolower($p_type);
		$queryStr = "ALTER TABLE ".$this->m_dbTableName." ADD COLUMN ".$this->m_dbColumnName;
		switch ($p_type) {
			case 'text':
			    $queryStr .= " VARCHAR(255) NOT NULL";
			    break;
			case 'date':
		    	$queryStr .= " DATE NOT NULL";
		    	break;
			case 'body':
                $isContent = (int)$p_isContent;
			    $queryStr .= " MEDIUMBLOB NOT NULL";
		    	break;
			case 'topic':
				$queryStr .= " INTEGER UNSIGNED NOT NULL";
				$queryStr2 = "INSERT INTO TopicFields (ArticleType, FieldName, RootTopicId) "
							."VALUES ('".$this->m_articleTypeName."', '".$this->m_fieldName."', '"
							.$p_rootTopicId ."')";
				if (!$g_ado_db->Execute($queryStr2)) {
					return false;
				}
				break;
		    default:
		    	return false;
		}
		$success = $g_ado_db->Execute($queryStr);
		if ($success) {
			$success = 0;
			$weight = $this->getNextOrder();
			$queryStr = "INSERT INTO ArticleTypeMetadata (type_name, field_name, field_type, field_weight, is_content_field) VALUES ('". $this->m_articleTypeName ."','". $this->m_fieldName ."', '". $p_type ."', $weight, '$isContent')";
			$success = $g_ado_db->Execute($queryStr);
		}

		if ($success) {

			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type field $1 created', $this->m_fieldName);
			Log::Message($logtext, null, 71);
		}
		return $success;
	} // fn create


	/**
     * Changes the type of the ATF.
     *
     * @param string p_type (text|date|body|topic)
     */
	public function setType($p_type, $p_rootTopicId)
	{
		global $g_ado_db;
		$p_type = strtolower($p_type);
		$queryStr = "ALTER TABLE ".$this->m_dbTableName." CHANGE ".$this->m_dbColumnName ." ". $this->m_dbColumnName;
		switch ($p_type) {
			case 'text':
			    $queryStr .= " VARCHAR(255) NOT NULL";
			    break;
			case 'date':
		    	$queryStr .= " DATE NOT NULL";
		    	break;
			case 'body':
		    	$queryStr .= " MEDIUMBLOB NOT NULL";
		    	break;
			case 'topic':
				$queryStr .= " INTEGER UNSIGNED NOT NULL";
				$queryStr2 = "UPDATE TopicFields SET RootTopicId=". $p_rootTopicId ." WHERE ArticleType='". $this->m_articleTypeName ."' AND FieldName='". $this->m_fieldName ."'";
				if (!$g_ado_db->Execute($queryStr2)) {
				    return false;
				}
				break;
		    default:
		    	return false;
		}
		$success = $g_ado_db->Execute($queryStr);
		if ($success) {
			$success = 0;
			$queryStr = "UPDATE ArticleTypeMetadata SET field_type='". $p_type ."' WHERE type_name='". $this->m_articleTypeName ."' AND field_name='". $this->m_fieldName ."'";
			$success = $g_ado_db->Execute($queryStr);
		}

		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type field $1 changed', $this->m_fieldName);
			Log::Message($logtext, null, 71);
		}
		return $success;
	} // fn setType


	/**
	 * @return boolean
	 */
	public function exists()
	{
		global $g_ado_db;
		$queryStr = "SHOW COLUMNS FROM ".$this->m_dbTableName." LIKE '".$this->m_dbColumnName."'";
		$exists = $g_ado_db->GetOne($queryStr);
		if ($exists) {
			return true;
		} else {
			return false;
		}
	} // fn exists


	/**
	 * @return void
	 */
	public function fetch($p_recordSet = null)
	{
		global $g_ado_db;
		if (!is_null($p_recordSet)) {
			foreach ($p_recordSet as $key => $value) {
				$this->$key = $value;
			}
		} else {
			$queryStr = 'SHOW COLUMNS FROM '.$this->m_dbTableName
						." LIKE '".$this->m_dbColumnName."'";
			$row = $g_ado_db->GetAll($queryStr);
			if (!is_null($row) && is_array($row) && sizeof($row) > 0 && !is_null($row[0])) {
				$this->fetch($row[0]);
			}
		}
		if (!empty($this->Field)) {
    		$this->m_fieldName = substr($this->Field, 1);
		}
		$this->m_dbColumnName = 'F'.$this->m_fieldName;
	} // fn fetch


	/**
	* Deletes an ATF
	*/
	public function delete()
	{
		global $g_ado_db;

		$orders = $this->getOrders();
		$queryStr = "ALTER TABLE ".$this->m_dbTableName." DROP COLUMN ".$this->m_dbColumnName;
		$success = $g_ado_db->Execute($queryStr);

		if ($success) {
			$success = 0;
			$queryStr = "DELETE FROM ArticleTypeMetadata "
			            ." WHERE type_name='". $this->m_articleTypeName ."'"
			            ." AND field_name='". $this->m_fieldName ."'";
			$success = $g_ado_db->Execute($queryStr);
		}

		// reorder
		if ($success) {

    	    //$mypos = array_keys($orders, $this->m_fieldName);
            $newOrders = array();
            foreach ($orders as $k => $v) {
                if ($v != $this->m_fieldName)
                    $newOrders[] = $v;

            }
            $newOrders = array_reverse($newOrders);
			$this->setOrders($newOrders);
		}

		if ($success) {

			$queryStr = "DELETE FROM TopicFields WHERE ArticleType = '".$this->m_articleTypeName
						."' and FieldName = '".substr($this->m_dbColumnName, 1)."'";
			$g_ado_db->Execute($queryStr);
			if (function_exists("camp_load_translation_strings")) {
			    camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type field $1 deleted', $this->m_fieldName);
			Log::Message($logtext, null, 72);
            return true;
		}
		return false;
	} // fn delete


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->Field;
	} // fn getName


	/**
	 * @return string
	 */
	public function getPrintName()
	{
		return substr($this->Field, 1);
	} // fn getPrintName


	/**
	 * @return string
	 */
	public function getType()
	{
		global $g_ado_db;
		$tmp = $this->Type;
		if (stristr($this->Type, 'int') != '') {
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    					.$this->m_articleTypeName."' and FieldName = '"
    					.substr($this->Field, 1)."'";
    		$topicId = $g_ado_db->GetOne($queryStr);
    		if ($topicId > 0) {
				return 'topic';
    		}
		}
		return strtolower($this->Type);
	} // fn getType


	/**
	 * @return string
	 */
	public function getTopicTypeRootElement()
	{
		global $g_ado_db;
		$topicId = null;
		if (stristr($this->Type, 'int') != '') {
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    					.$this->m_articleTypeName."' and FieldName = '"
    					.substr($this->Field, 1)."'";
    		$topicId = $g_ado_db->GetOne($queryStr);
		}
		return $topicId;
	}


	/**
	 * Get a human-readable representation of the column type.
	 * @return string
	 */
	public function getPrintType($p_languageId = 1)
	{
		global $g_ado_db;
		switch ($this->getType()) {
	    case 'mediumblob':
	    	return getGS('Multi-line Text with WYSIWYG');
	    case 'varchar(255)':
	    	return getGS('Single-line Text');
	    case 'varbinary(255)':
	    	return getGS('Single-line Text');
	    case 'date':
	    	return getGS('Date');
	    case 'topic':
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    					.$this->m_articleTypeName."' and FieldName = '"
    					.substr($this->Field, 1)."'";
    		$topicId = $g_ado_db->GetOne($queryStr);
   			$topic = new Topic($topicId);
   			$translations = $topic->getTranslations();
   			if (array_key_exists($p_languageId, $translations)) {
   				return "Topic (".$translations[$p_languageId].")";
   			} elseif ($p_languageId != 1 && array_key_exists(1, $translations)) {
   				return "Topic (".$translations[1].")";
   			} else {
   				return "Topic (".end($translations).")";
   			}
	    	break;
	    default:
	    	return "unknown";
		}
	} // fn getPrintType


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
		if (!$p_lang) {
			$lang = camp_session_get('LoginLanguageId', 1);
		} else {
			$lang = $p_lang;
		}
		$languageObj = new Language($lang);
		$translations = $this->getTranslations();
		if (!isset($translations[$lang])) {
		    return '';
		}
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
	public function getDisplayName($p_lang = 0)
	{
		if (!$p_lang) {
			$lang = camp_session_get('LoginLanguageId', 1);
		} else {
			$lang = $p_lang;
		}

		$translations = $this->getTranslations();
		if (!isset($translations[$lang])) {
		    return substr($this->Field, 1);
		}
		return $translations[$lang];
	} // fn getDisplayName


	/**
	 * Returns the is_hidden status of a field.  Returns 'hidden' or 'shown'.
	 *
	 * @return string (shown|hidden)
	 */
	public function getStatus()
	{
		if ($this->m_metadata['is_hidden']) {
		    return 'hidden';
		} else {
		    return 'shown';
		}
	} // fn getStatus


	/**
	 * @param string p_status (hide|show)
	 */
	public function setStatus($p_status)
	{
		global $g_ado_db;
		if ($p_status == 'show') {
		    $set = "is_hidden=0";
		}
		if ($p_status == 'hide') {
		    $set = "is_hidden=1";
		}
		$queryStr = "UPDATE ArticleTypeMetadata "
		            ." SET $set "
		            ." WHERE type_name='". $this->m_articleTypeName ."'"
		            ." AND field_name='". $this->m_fieldName ."'";
		$ret = $g_ado_db->Execute($queryStr);
	} // fn setStatus


	/**
	 * Return an associative array of the metadata in ArticleFieldMetadata.
	 *
	 * @return array
	 */
	public function getMetadata() {
		global $g_ado_db;
		$queryStr = "SELECT * FROM ArticleTypeMetadata "
		            ." WHERE type_name='". $this->m_articleTypeName ."'"
		            ." AND field_name='". $this->m_fieldName ."'";
		$queryArray = $g_ado_db->GetRow($queryStr);
		return $queryArray;
	} // fn getMetadata


	/**
	 * @return -1 OR int
	 */
	public function getPhraseId()
	{
		if (isset($this->m_metadata['fk_phrase_id'])) {
		    return $this->m_metadata['fk_phrase_id'];
		}
		return -1;
	} // fn getPhraseId()


	/**
	 * @return array
	 */
	public function getTranslations()
	{
		$return = array();
		$tmp = Translation::getTranslations($this->getPhraseId());
		foreach ($tmp as $k => $v) {
			$return[$k] = $v;
		}
		return $return;
	} // fn getTransltions

	
	public function isContent() {
	    return $this->m_metadata['is_content_field'];
	}
	


	public function setIsContent($p_isContent) {
        global $g_ado_db;
	    $queryStr = "UPDATE ArticleTypeMetadata "
                    ." SET is_content_field = " . (int)$p_isContent
                    ." WHERE type_name='". $this->m_articleTypeName ."'"
                    ." AND field_name='". $this->m_fieldName ."'";
        $ret = $g_ado_db->Execute($queryStr);
	}


	/**
	 * Quick lookup to see if the current language is already translated for this article type: used by delete and update in setName
	 * returns 0 if no translation or the phrase_id if there is one.
	 *
	 * @param int p_languageId
	 *
	 * @return 0 or phrase id (int)
	 */
	public function translationExists($p_languageId) {
		global $g_ado_db;
		$sql = "SELECT atm.*, t.* FROM ArticleTypeMetadata atm, Translations t WHERE atm.type_name='". $this->m_articleTypeName ."' AND atm.field_name='". $this->m_fieldName ."' AND atm.fk_phrase_id = t.phrase_id AND t.fk_language_id = '$p_languageId'";
		$row = $g_ado_db->getAll($sql);
		if (count($row)) {
		    return $row[0]['fk_phrase_id'];
		} else {
		    return 0;
		}
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
	public function setName($p_languageId, $p_value)
	{
		global $g_ado_db;
		if (!is_numeric($p_languageId)) {
			return false;
		}
		// if the string is empty, nuke it
		if (!is_string($p_value) || $p_value == '') {
			if ($phrase_id = $this->translationExists($p_languageId)) {
			    $trans = new Translation($p_languageId, $phrase_id);
			    $trans->delete();
				$changed = true;
			} else {
			    $changed = false;
			}
		} else if ($phrase_id = $this->translationExists($p_languageId)) {
			// just update
			$description = new Translation($p_languageId, $phrase_id);
			$description->setText($p_value);
			$changed = true;
		} else {
			// Insert the new translation.
			// first get the fk_phrase_id
			$sql = "SELECT fk_phrase_id FROM ArticleTypeMetadata"
			       ." WHERE type_name='". $this->m_articleTypeName ."'"
			       ." AND field_name='". $this->m_fieldName ."'";
			$row = $g_ado_db->GetRow($sql);
			// if this is the first translation ...
			if (!is_numeric($row['fk_phrase_id'])) {
				$description = new Translation($p_languageId);
				$description->create($p_value);
				$phrase_id = $description->getPhraseId();
				// if the phrase_id isn't there, insert it.
				$sql = "UPDATE ArticleTypeMetadata "
				       ." SET fk_phrase_id=".$phrase_id
				       ." WHERE type_name='". $this->m_articleTypeName ."'"
				       ." AND field_name='". $this->m_fieldName ."'";
				$changed = $g_ado_db->Execute($sql);
			} else {
				// if the phrase is already translated into atleast one language, just reuse that fk_phrase_id
				$desc = new Translation($p_languageId, $row['fk_phrase_id']);
				$desc->create($p_value);
				$changed = true;
			}
		}

		if ($changed) {
			if (function_exists("camp_load_translation_strings")) {
			    camp_load_translation_strings("api");
			}
			$logtext = getGS('Field $1 updated', $this->m_dbColumnName);
			Log::Message($logtext, null, 143);
		}

		return $changed;
	} // fn setName


	/**
	 * Returns the highest weight + 1 or 0 for the starter
	 *
	 * @return int
	 */
	public function getNextOrder()
	{
		global $g_ado_db;
		$queryStr = "SELECT field_weight "
		            ." FROM ArticleTypeMetadata "
		            ." WHERE type_name='". $this->m_articleTypeName ."'"
		            ." AND field_name != 'NULL' "
		            ." ORDER BY field_weight DESC LIMIT 1";
		$row = $g_ado_db->getRow($queryStr);
		if (isset($row['field_weight'])) {
			$next = $row['field_weight'] + 1;
	    } else {
		    $next = 0;
		}
		return ($next);
	} // fn getNextOrder



	/**
	 * Get the ordering of all fields; initially, a field has a field_weight
	 * of NULL when it is created.  if we discover that a field has a field
	 * weight of NULL, we give it the MAX+1 field_weight.  Returns a NUMERIC
	 * array of ORDER => FIELDNAME.
	 *
	 * @return array
	 */
	public function getOrders()
	{
		global $g_ado_db;
		$queryStr = "SELECT field_weight, field_name "
		            ." FROM ArticleTypeMetadata "
		            ." WHERE type_name='". $this->m_articleTypeName ."'"
		            ." AND field_name != 'NULL' "
		            ." ORDER BY field_weight DESC";
		$queryArray = $g_ado_db->GetAll($queryStr);
		$orderArray = array();
		foreach ($queryArray as $row => $values) {
			if ($values['field_weight'] == NULL) {
			    $values['field_weight'] = $this->getNextOrder();
			}
			$orderArray[$values['field_weight']] = $values['field_name'];
		}
		return $orderArray;
	} // fn getOrders


	/**
	 * Saves the ordering of all the fields.  Accepts an NUMERIC array of
	 * ORDERRANK => FIELDNAME. (see getOrders)
	 *
	 * @param array orderArray
	 */
	public function setOrders($orderArray)
	{
		global $g_ado_db;
		foreach ($orderArray as $order => $field) {
			$queryStr = "UPDATE ArticleTypeMetadata "
			            ." SET field_weight=$order "
			            ." WHERE type_name='". $this->m_articleTypeName ."'"
			            ." AND field_name='". $field ."'";
			$g_ado_db->Execute($queryStr);
		}
	} // fn setOrders


	/**
     * Reorders the current field; accepts either "up" or "down"
     *
     * @param string move (up|down)
	 */
	public function reorder($move)
	{
		$orders = $this->getOrders();

		$tmp = array_keys($orders, $this->m_fieldName);
		$pos = $tmp[0];
		if ($pos == 0 && $move == 'up') {
		    return;
		}
		if ( ($pos == count($orders) - 1) && ($move == 'down')) {
		    return;
		}
		if ($move == 'down') {
			$tmp = $orders[$pos + 1];
			$orders[$pos + 1] = $orders[$pos];
			$orders[$pos] = $tmp;
		}
		if ($move == 'up') {
			$tmp = $orders[$pos - 1];
			$orders[$pos - 1] = $orders[$pos];
			$orders[$pos] = $tmp;
		}

		$this->setOrders($orders);
	} // fn reorder


	/**
	 * Returns an array of fields from all article types that match
	 * the given conditions.
	 *
	 * @param $p_name
	 *         if specified returns fields with the given name
	 * @param $p_articleType
	 *         if specified returns fields of the given article type
	 * @param $p_dataType
	 *         if specified returns the fields having the given data type
	 *
	 * @return array
	 */
	public static function FetchFields($p_name = null, $p_articleType = null,
	                                   $p_dataType = null)
	{
	    global $g_ado_db;

	    if (isset($p_name)) {
	        $whereClauses[] = "field_name = '" . $g_ado_db->escape($p_name) . "'";
	    }
	    if (isset($p_articleType)) {
	        $whereClauses[] = "type_name = '" . $g_ado_db->escape($p_articleType) . "'";
	    }
	    if (isset($p_dataType)) {
	        $whereClauses[] = "field_type = '" . $g_ado_db->escape($p_dataType) . "'";
	    }
	    $query = 'SELECT * FROM ArticleTypeMetadata WHERE '
	             . implode(' and ', $whereClauses)
	             . ' ORDER BY type_name ASC, field_name ASC';
	    $rows = $g_ado_db->GetAll($query);
	    $fields = array();
	    foreach ($rows as $row) {
	        $fields[] = new ArticleTypeField($row['type_name'], $row['field_name']);
	    }
	    return $fields;
	}

} // class ArticleTypeField

?>