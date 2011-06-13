<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Topic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');


/**
 * @package Campsite
 */
class ArticleTypeField extends DatabaseObject {
    const TYPE_TEXT = 'text';
    const TYPE_BODY = 'body';
    const TYPE_DATE = 'date';
    const TYPE_TOPIC = 'topic';
    const TYPE_SWITCH = 'switch';
    const TYPE_NUMERIC = 'numeric';

    const NUMERIC_DEFAULT_DIGITS = 65;
    const NUMERIC_DEFAULT_PRECISION = 2;

    var $m_dbTableName = 'ArticleTypeMetadata';
	var $m_keyColumnNames = array('type_name', 'field_name');
    var $m_columnNames = array(
        'type_name',
        'field_name',
        'field_weight',
        'is_hidden',
        'comments_enabled',
        'fk_phrase_id',
        'field_type',
        'field_type_param',
        'is_content_field',
        'max_size');
    private $m_rootTopicId = null;
    private $m_precision = null;


	public function __construct($p_articleTypeName = null, $p_fieldName = null)
	{
		$this->m_data['type_name'] = $p_articleTypeName;
		$this->m_data['field_name'] = $p_fieldName;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
	} // constructor


	/**
	 * Returns the article type name.
	 *
	 * @return string
	 */
	public function getArticleType()
	{
		return $this->m_data['type_name'];
	} // fn getArticleType


	/**
	 * Rename the article type field.
	 *
	 * @param string p_newName
	 *
	 */
	public function rename($p_newName)
	{
		global $g_ado_db;
		if (!$this->exists() || !ArticleType::isValidFieldName($p_newName)) {
			return 0;
		}

		$types = self::DatabaseTypes(null, $this->m_precision);

		$queryStr = "ALTER TABLE `X". $this->m_data['type_name']."` CHANGE COLUMN `"
		. $this->getName() ."` `F$p_newName` ". $types[$this->getType()];
		$success = $g_ado_db->Execute($queryStr);

		if ($success) {
			if ($this->getType() == self::TYPE_TOPIC) {
				$query = "UPDATE TopicFields SET FieldName = '" . $g_ado_db->escape($p_newName)
				. "' WHERE RootTopicId = " . $this->getTopicTypeRootElement();
				$g_ado_db->Execute($query);
			}

			$fieldName = $this->m_data['field_name'];
			$this->setProperty('field_name', $p_newName);

			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logText = getGS('The article type field "$1" has been renamed to "$2".',
			$fieldName, $p_newName);
			Log::Message($logText, null, 62);
		}
	} // fn rename


    /**
     * Fetch a single record from the database for the given key.
     *
     * @param array $p_recordSet
     *      If the record has already been fetched and we just need to
     *      assign the data to the object's internal member variable.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
	public function fetch($p_recordSet = null)
	{
		$success = parent::fetch($p_recordSet);
		if ($success && $this->getType() == self::TYPE_NUMERIC) {
			$params = explode('=', $this->m_data['field_type_param']);
			if (isset($params[0]) && $params[0] == 'precision') {
				$this->m_precision = (int) $params[1];
			}
		}
		return $success;
	}


	/**
	 * Create a column in the table.
	 * @param string $p_type
	 *		Can be one of: 'text', 'date', 'body', 'switch', 'numeric'.
	 */
	public function create($p_type, array $p_params = array())
	{
		global $g_ado_db;

        $p_type = strtolower($p_type);
        $numericPrecision = isset($p_params['precision']) ? $p_params['precision'] : null;
        $types = self::DatabaseTypes(null, $numericPrecision);
		if ($this->getPrintName() != 'NULL' && !array_key_exists($p_type, $types)) {
			return false;
		}

		if ($p_type == self::TYPE_TOPIC && $this->getPrintName() != 'NULL') {
			if (!isset($p_params['root_topic_id']) && !is_numeric($p_params['root_topic_id'])) {
				return false;
			}
			$rootTopicId = (int)$p_params['root_topic_id'];
			$queryStr2 = "INSERT INTO TopicFields (ArticleType, FieldName, RootTopicId) "
			. "VALUES ('".$g_ado_db->escape($this->m_data['type_name']) . "', '"
			. $g_ado_db->escape($this->m_data['field_name']) . "', '$rootTopicId')";
			if (!$g_ado_db->Execute($queryStr2)) {
				return false;
			}
		}

		if ($this->getPrintName() != 'NULL') {
			$queryStr = "ALTER TABLE `X" . $this->m_data['type_name'] . "` ADD COLUMN `"
			. $this->getName() . '` ' . $types[$p_type];
			$success = $g_ado_db->Execute($queryStr);
		}
		if ($this->getPrintName() == 'NULL' || $success) {
			$data = array();
			if ($this->getPrintName() != 'NULL') {
				if ($p_type == self::TYPE_BODY && isset($p_params['is_content'])) {
                    $data['is_content_field'] = (int)$p_params['is_content'];
				}
				if ($p_type == self::TYPE_NUMERIC && isset($p_params['precision'])) {
					$data['field_type_param'] = 'precision=' . (int)$p_params['precision'];
				}
				if ($p_type == self::TYPE_TEXT && isset($p_params['maxsize'])) {
                    $data['max_size'] = (int)$p_params['maxsize'];
                }
				$data['field_type'] = $p_type;
				$data['field_weight'] = $this->getNextOrder();
			}
			$success = parent::create($data);
		}

		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type field "$1" created', $this->m_data['field_name']);
			Log::Message($logtext, null, 71);
            CampCache::singleton()->clear('user');
		}
		return $success;
	} // fn create


	/**
	 * Returns an array of types compatible with the given field type.
	 * @return array
	 */
	public static function TypesConvertibleTo($p_type)
	{
		switch ($p_type) {
			case self::TYPE_BODY:
				return array(self::TYPE_TEXT, self::TYPE_DATE, self::TYPE_TOPIC, self::TYPE_SWITCH, self::TYPE_NUMERIC);
			case self::TYPE_TEXT:
				return array(self::TYPE_DATE, self::TYPE_TOPIC, self::TYPE_SWITCH, self::TYPE_NUMERIC);
			case self::TYPE_DATE:
				return array();
			case self::TYPE_TOPIC:
				return array();
			case self::TYPE_SWITCH:
				return array();
			case self::TYPE_NUMERIC:
				return array();
		}
		return false;
	}


	public static function TypesConvertibleFrom($p_type)
	{
		$allTypes = self::DatabaseTypes();
		if (!array_key_exists($p_type, $allTypes)) {
			return false;
		}
		$convertibleFromTypes = array();
		foreach ($allTypes as $typeName=>$sqlDesc) {
			if (array_search($p_type, self::TypesConvertibleTo($typeName)) !== false) {
				$convertibleFromTypes[] = $typeName;
			}
		}
		return $convertibleFromTypes;
	}


	public function getConvertibleFromTypes()
	{
		return self::TypesConvertibleTo($this->getType());
	}


	public function getConvertibleToTypes()
	{
		return self::TypesConvertibleFrom($this->getType());
	}


	/**
	 * Returns true if the given type can be converted to the current field type.
	 * @param $p_type
	 * @return boolean
	 */
	public function isConvertibleFrom($p_type)
	{
		if (is_object($p_type) && get_class($p_type) == __CLASS__) {
			if ($this->getType() == 'topic' && $p_type->getType() == 'topic') {
				return $this->getTopicTypeRootElement() == $p_type->getTopicTypeRootElement();
			}
			$p_type = $p_type->getType();
		}
		return ($this->getType() == $p_type && $p_type != 'topic')
		|| array_search($p_type, $this->getConvertibleFromTypes()) !== false;
	}


	public function isConvertibleTo($p_type)
	{
		return array_search($p_type, $this->getConvertibleToTypes());
	}


	/**
     * Changes the type of the field
     *
     * @param string p_type (text|date|body|topic|switch|numeric)
     */
	public function setType($p_type)
	{
		global $g_ado_db;

		$p_type = strtolower($p_type);
        $types = self::DatabaseTypes();
        if (!array_key_exists($p_type, $types)) {
            return false;
        }
        if ($this->getType() == $p_type) {
        	return true;
        }

        if ($this->getType() == self::TYPE_TOPIC) {
        	$queryStr = "DELETE FROM TopicFields WHERE ArticleType = '"
        	. $g_ado_db->escape($this->m_data['type_name'])
            ."' AND FieldName = '". $g_ado_db->escape($this->m_data['field_name']) ."'";
            if (!$g_ado_db->Execute($queryStr)) {
                return false;
            }
        }
        $queryStr = "ALTER TABLE `X" . $this->m_data['type_name'] . "` MODIFY `"
        . $this->getName() . '` ' . $types[$p_type];
		$success = $g_ado_db->Execute($queryStr);
		if ($success) {
			$this->setProperty('field_type_param', null);
			$success = $this->setProperty('field_type', $p_type);
            $this->m_rootTopicId = null;
		}

		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type field "$1" changed', $this->m_data['field_name']);
			Log::Message($logtext, null, 71);
		}
		return $success;
	} // fn setType


	/**
	 * Deletes the current article type field.
	 */
	public function delete()
	{
		global $g_ado_db;

		if (!$this->exists()) {
			return false;
		}

		$orders = $this->getOrders();

        $translation = new Translation(null, $this->getPhraseId());
        $translation->deletePhrase();

        if ($this->getPrintName() != 'NULL') {
			$queryStr = "ALTER TABLE `X" . $this->m_data['type_name']
			. "` DROP COLUMN `" . $this->getName() . "`";
			$success = $g_ado_db->Execute($queryStr);
		}

		if ($success || $this->getPrintName() == 'NULL') {
            $myType = $this->getType();
			if ($myType == self::TYPE_TOPIC) {
                $queryStr = "DELETE FROM TopicFields WHERE ArticleType = '"
                . $g_ado_db->escape($this->m_data['type_name']) . "' and FieldName = '"
                . $g_ado_db->escape($this->m_data['field_name']) . "'";
                $g_ado_db->Execute($queryStr);
                $this->m_rootTopicId = null;
            }
			$fieldName = $this->m_data['field_name'];
			$success = parent::delete();
		}

		// reorder
		if ($success) {
            $newOrders = array();
            foreach ($orders as $k => $v) {
                if ($v != $this->m_data['field_name'])
                    $newOrders[] = $v;

            }
            $newOrders = array_reverse($newOrders);
			$this->setOrders($newOrders);

			if (function_exists("camp_load_translation_strings")) {
			    camp_load_translation_strings("api");
			}
			$logtext = getGS('Article type field "$1" deleted', $fieldName);
			Log::Message($logtext, null, 72);
            CampCache::singleton()->clear('user');
		}
		return $success;
	} // fn delete


	/**
	 * @return string
	 */
	public function getName()
	{
		return 'F'.$this->m_data['field_name'];
	} // fn getName

	/**
     * Gets the maximum size of a article type
	 * @return int
	 */
	public function getMaxSize()
	{
		return $this->m_data['max_size'];
	} // fn getMaxSize

	/**
	 * @return string
	 */
	public function getPrintName()
	{
		return $this->m_data['field_name'];
	} // fn getPrintName


	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->m_data['field_type'];
	} // fn getType


	public function getGenericType()
	{
		switch ($this->getType()) {
			case self::TYPE_BODY:
			case self::TYPE_TEXT:
				return 'string';
			case self::TYPE_DATE:
				return 'date';
			case self::TYPE_NUMERIC:
				return 'integer';
			case self::TYPE_SWITCH:
				return 'switch';
			case self::TYPE_TOPIC:
				return 'topic';
		}
		return null;
	}


	/**
	 * @return string
	 */
	public function getTopicTypeRootElement()
	{
		global $g_ado_db;

		if ($this->getType() == self::TYPE_TOPIC && is_null($this->m_rootTopicId)) {
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    		. $g_ado_db->escape($this->getArticleType()) . "' and FieldName = '"
    		. $g_ado_db->escape($this->getPrintName()) . "'";
    		$this->m_rootTopicId = $g_ado_db->GetOne($queryStr);
		}
		return $this->m_rootTopicId;
	}


	/**
	 * Get a human-readable representation of the column type.
	 * @return string
	 */
	public static function VerboseTypeName($p_typeName, $p_languageId = 1, $p_rootTopicId = null)
	{
		switch ($p_typeName) {
	    case self::TYPE_BODY:
	    	return getGS('Multi-line Text with WYSIWYG');
	    case self::TYPE_TEXT:
	    	return getGS('Single-line Text');
	    case self::TYPE_DATE:
	    	return getGS('Date');
	    case self::TYPE_TOPIC:
	    	if (is_null($p_rootTopicId)) {
	    		return getGS('Topic');
	    	}
   			$topic = new Topic($p_rootTopicId);
   			$translations = $topic->getTranslations();
   			if (array_key_exists($p_languageId, $translations)) {
   				return getGS('Topic') . ' (' . $translations[$p_languageId] . ')';
   			} elseif ($p_languageId != 1 && array_key_exists(1, $translations)) {
   				return getGS('Topic') . ' (' . $translations[1] . ')';
   			} else {
   				return getGS('Topic') . ' (' . end($translations) . ')';
   			}
	    	break;
	    case self::TYPE_SWITCH:
	    	return getGS('Switch');
	    case self::TYPE_NUMERIC:
	    	return getGS('Numeric');
	    default:
	    	return getGS("unknown");
		}
	} // fn VerboseTypeName


	public function getVerboseTypeName($p_languageId = 1)
	{
		$rootTopicId = $this->getType() == self::TYPE_TOPIC ? $this->getTopicTypeRootElement() : null;
		return self::VerboseTypeName($this->getType(), $p_languageId, $rootTopicId);
	}


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
		    return $this->getPrintName();
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
		if ($this->m_data['is_hidden']) {
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
		if ($p_status == 'show') {
			$hidden = 0;
		} elseif ($p_status == 'hide') {
			$hidden = 1;
		} else {
			return null;
		}
		return $this->setProperty('is_hidden', $hidden);
	} // fn setStatus


	/**
	 * Return an associative array of the metadata in ArticleFieldMetadata.
	 *
	 * @return array
	 */
	public function getMetadata() {
		return $this->m_data;
	} // fn getMetadata


	/**
	 * @return -1 OR int
	 */
	public function getPhraseId()
	{
		if (isset($this->m_data['fk_phrase_id'])) {
		    return $this->m_data['fk_phrase_id'];
		}
		return -1;
	} // fn getPhraseId()


	/**
	 * Returns an array of translation strings for the field name.
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


    /**
     * Returns true if the current field is hidden.
     * @return boolean
     */
	public function isHidden()
	{
		return $this->m_data['is_hidden'];
	}


	/**
	 * Returns true if the current field is a content field.
	 * @return boolean
	 */
	public function isContent() {
	    return $this->m_data['is_content_field'];
	}



	/**
	 * Sets the content flag. Returns true on success, false otherwise.
	 * @param $p_isContent
	 * @return boolean
	 */
	public function setIsContent($p_isContent)
	{
		return $this->setProperty('is_content_field', (int)$p_isContent);
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
		if (!isset($this->m_data['fk_phrase_id'])) {
			return false;
		}
		$translation = new Translation($p_languageId, $this->m_data['fk_phrase_id']);
		return $translation->exists();
	} // fn translationExists


	/**
	 * Set the type name for the given language.  A new entry in
	 * the database will be created if the language did not exist.
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
		} else {
			$description = new Translation($p_languageId, $this->getProperty('fk_phrase_id'));
			if ($description->exists()) {
                $changed = $description->setText($p_value);
			} else {
				$changed = $description->create($p_value);
				if ($changed && is_null($this->getProperty('fk_phrase_id'))) {
					$this->setProperty('fk_phrase_id', $description->getPhraseId());
				}
			}
		}

		if ($changed) {
			if (function_exists("camp_load_translation_strings")) {
			    camp_load_translation_strings("api");
			}
			$logtext = getGS('Field "$1" updated', $this->m_data['field_name']);
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
		$queryStr = "SELECT field_weight FROM `" . $this->m_dbTableName
		. "` WHERE type_name = '" . $g_ado_db->escape($this->m_data['type_name']) . "'"
		. " AND field_name != 'NULL' ORDER BY field_weight DESC";
		$field_weight = $g_ado_db->GetOne($queryStr);
		if (!is_null($field_weight)) {
			$next = $field_weight + 1;
	    } else {
		    $next = 1;
		}
		return $next;
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

		$queryStr = "SELECT field_weight, field_name FROM `" . $this->m_dbTableName
		. "` WHERE type_name = '" . $g_ado_db->escape($this->m_data['type_name'])
		. "' AND field_name != 'NULL' ORDER BY field_weight DESC";
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
			$field = new ArticleTypeField($this->m_data['type_name'], $field);
			if ($field->exists()) {
				$field->setProperty('field_weight', $order);
			}
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

		$tmp = array_keys($orders, $this->m_data['field_name']);
		if (count($tmp) == 0) {
			return;
		}
		$pos = $tmp[0];

		reset($orders);
		list($max, $value) = each($orders);
		end($orders);
		list($min, $value) = each($orders);

		if ($pos <= $min && $move == 'up') {
		    return;
		}
		if ($pos >= $max && $move == 'down') {
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
	$p_dataType = null, $p_negateName = false, $p_negateArticleType = false,
	$p_negateDataType = false, $p_selectHidden = true, $p_skipCache = false)
	{
	    global $g_ado_db;

	    if (!$p_skipCache && CampCache::IsEnabled()) {
	    	$paramsArray['name'] = (is_null($p_name)) ? 'null' : $p_name;
	    	$paramsArray['article_type'] = (is_null($p_articleType)) ? 'null' : $p_articleType;
	    	$paramsArray['data_type'] = (is_null($p_dataType)) ? 'null' : $p_dataType;
	    	$paramsArray['negate_name'] = ($p_negateName == false) ? 'false' : 'true';
	    	$paramsArray['negate_article_type'] = ($p_negateArticleType == false) ? 'false' : 'true';
	    	$paramsArray['negate_data_type'] = ($p_negateDataType == false) ? 'false' : 'true';
	    	$paramsArray['select_hidden'] = ($p_selectHidden == false)? 'false' : 'true';
	    	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
	    	$articleTypeFieldsList = $cacheListObj->fetchFromCache();
	    	if ($articleTypeFieldsList !== false
	    	&& is_array($articleTypeFieldsList)) {
	    		return $articleTypeFieldsList;
	    	}
	    }

	    $whereClauses = array();
	    if (isset($p_name)) {
	    	$operator = $p_negateName ? '<>' : '=';
	        $whereClauses[] = "field_name $operator '" . $g_ado_db->escape($p_name) . "'";
	    }
	    if (isset($p_articleType)) {
            $operator = $p_negateArticleType ? '<>' : '=';
	    	$whereClauses[] = "type_name $operator '" . $g_ado_db->escape($p_articleType) . "'";
	    }
	    if (isset($p_dataType)) {
            $operator = $p_negateDataType ? '<>' : '=';
	    	$whereClauses[] = "field_type $operator '" . $g_ado_db->escape($p_dataType) . "'";
	    }
	    if (!$p_selectHidden) {
	    	$whereClauses[] = 'is_hidden = false';
	    }
	    $where = count($whereClauses) > 0 ? ' WHERE ' . implode(' and ', $whereClauses) : null;
	    $query = "SELECT * FROM `ArticleTypeMetadata` $where ORDER BY type_name ASC, field_weight ASC";
	    $rows = $g_ado_db->GetAll($query);
	    $fields = array();
	    foreach ($rows as $row) {
	    	$field = new ArticleTypeField($row['type_name'], $row['field_name']);
	    	if ($field->getPrintName() == '') {
	    		$field->delete();
	    		continue;
	    	}
	        $fields[] = $field;
	    }
	    if (!$p_skipCache && CampCache::IsEnabled()) {
	        $cacheListObj->storeInCache($fields);
	    }

	    return $fields;
	} // fn FetchFields


	/**
	 * Returns an array of valid field data types.
	 * @return array
	 */
	public static function DatabaseTypes($p_numericDigits = null, $p_numericPrecision = null)
	{
		$p_numericDigits = is_null($p_numericDigits) ? self::NUMERIC_DEFAULT_DIGITS : $p_numericDigits;
		$p_numericPrecision = is_null($p_numericPrecision) ? self::NUMERIC_DEFAULT_PRECISION : $p_numericPrecision;
		settype($p_numericDigits, 'integer');
		settype($p_numericPrecision, 'integer');
		$numericDef = "NUMERIC($p_numericDigits, $p_numericPrecision) NOT NULL";

		$types = array(self::TYPE_TEXT=>'VARCHAR(255) NOT NULL',
			 self::TYPE_BODY=>'MEDIUMBLOB NOT NULL',
			 self::TYPE_DATE=>'DATE NOT NULL',
			 self::TYPE_TOPIC=>'INTEGER UNSIGNED NOT NULL',
			 self::TYPE_SWITCH=>'BOOLEAN NOT NULL',
			 self::TYPE_NUMERIC=>$numericDef);
        return $types;
	}
} // class ArticleTypeField

?>