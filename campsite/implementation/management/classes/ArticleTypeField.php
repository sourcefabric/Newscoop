<?php

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/ParserCom.php');
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
	
	function ArticleTypeField($p_articleTypeName = null, $p_fieldName = null)
	{
		$this->m_articleTypeName = $p_articleTypeName;
		$this->m_fieldName = $p_fieldName;
		$this->m_dbTableName = "X".$p_articleTypeName;
		$this->m_dbColumnName = "F".$p_fieldName;
		if (!is_null($this->m_articleTypeName) && !is_null($this->m_fieldName)) {
			$this->fetch();
		}
		$this->m_metadata = $this->getMetadata();

	} // constructor


	/**
	 * @return string
	 */
	function getDbTableName()
	{
		return $this->m_dbTableName;
	} // fn getDbTableName

	/**
	 * Rename the article type.  This will move the entire table in the database and update ArticleTypeMetadata.
	 * Usually, one wants to just rename the Display Name, which is done via SetDisplayName
	 *
	 */
	function rename($p_newName)
	{
		global $Campsite;
		if (!ArticleType::isValidFieldName($p_newName)) return 0;
		// TODO: This sql sequence could be cleaned up for efficiency.  Renaming columns is tricky in mysql. pjh 2006/March
		$queryStr = "SHOW COLUMNS FROM ". $this->m_dbTableName;
		$success = 0;
		$res = mysql_query($queryStr);
		if (!$res) 
			return;

		$queryStr = 0;
			
	    if (mysql_num_rows($res) > 0) {
	    	while ($row = mysql_fetch_assoc($res)) {
	    		if ($row['Field'] == $this->m_dbColumnName) {
					$queryStr = "ALTER TABLE ". $this->m_dbTableName ." CHANGE COLUMN ". $this->m_dbColumnName ." F". $p_newName ." ". $row['Type']; 				
					break;
	    		}
	    	}
		}

		if ($queryStr) {
			$success = $Campsite['db']->Execute($queryStr);
			
		}

		if ($success) {
			$queryStr = "UPDATE ArticleTypeMetadata SET field_name='F". $p_newName ."' WHERE field_name='". $this->m_dbColumnName ."'";
			$success2 = $Campsite['db']->Execute($queryStr);		
		}


		if ($success2) {
			$this->m_dbColumnName = 'F'. $p_newName;
			if (function_exists("camp_load_language")) { camp_load_language("api"); }
			$logText = getGS('The article type field $1 has been renamed to $2.', $this->m_dbColumnName, $p_newName);
			Log::Message($logText, null, 62);
			//ParserCom::SendMessage('article_type_fields', 'rename', array('article_field' => $this->m_dbColumnName));
		}
	}



	/**
	 * Create a column in the table.
	 * @param string $p_type
	 *		Can be one of: 'text', 'date', 'body'.
	 */
	function create($p_type, $p_rootTopicId = 0)
	{
		global $Campsite;
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
		    	$queryStr .= " MEDIUMBLOB NOT NULL";
		    	break;
			case 'topic':
				$queryStr .= " INTEGER UNSIGNED NOT NULL";
				$queryStr2 = "INSERT INTO TopicFields (ArticleType, FieldName, RootTopicId) "
							."VALUES ('".$this->m_articleTypeName."', '".$this->m_fieldName."', '"
							.$p_rootTopicId ."')";
				if (!$Campsite['db']->Execute($queryStr2)) {
					return false;
				}
				break;
		    default:
		    	return false;
		}
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			$success = 0;
			$queryStr = "INSERT INTO ArticleTypeMetadata (type_name, field_name, field_type, is_hidden) VALUES ('". $this->m_dbTableName ."','". $this->m_dbColumnName ."', '". $p_type ."', 0)";
			$success = $Campsite['db']->Execute($queryStr);
		
		}

		if ($success) {

			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Article type field $1 created', $this->m_dbColumnName);
			Log::Message($logtext, null, 71);
			ParserCom::SendMessage('article_types', 'modify', array("article_type"=> $this->m_articleTypeName));
		}
		return $success;
	} // fn create


	function setType($p_type) {
		global $Campsite;
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
				$queryStr2 = "INSERT INTO TopicFields (ArticleType, FieldName, RootTopicId) "
							."VALUES ('".$this->m_articleTypeName."', '".$this->m_fieldName."', '"
							.$p_rootTopicId ."')";
				if (!$Campsite['db']->Execute($queryStr2)) {
					return false;
				}
				break;
		    default:
		    	return false;
		}
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			$success = 0;
			$queryStr = "UPDATE ArticleTypeMetadata SET field_type='". $p_type ."' WHERE type_name='". $this->m_dbTableName ."' AND field_name='". $this->m_dbColumnName ."'";
			$success = $Campsite['db']->Execute($queryStr);
		}

		if ($success) {

			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Article type field $1 changed', $this->m_dbColumnName);
			Log::Message($logtext, null, 71);
			ParserCom::SendMessage('article_types', 'modify', array("article_type"=> $this->m_articleTypeName));
		}
		return $success;
				
	
	}
	/**
	 * @return boolean
	 */
	function exists()
	{
		global $Campsite;
		$queryStr = "SHOW COLUMNS FROM ".$this->m_dbTableName." LIKE '".$this->m_dbColumnName."'";
		$exists = $Campsite['db']->GetOne($queryStr);
		if ($exists) {
			return true;
		} else {
			return false;
		}
	} // fn exists


	/**
	 * @return void
	 */
	function fetch($p_recordSet = null)
	{
		global $Campsite;
		if (!is_null($p_recordSet)) {
			foreach ($p_recordSet as $key => $value) {
				$this->$key = $value;
			}
		} else {
			$queryStr = 'SHOW COLUMNS FROM '.$this->m_dbTableName
						." LIKE '".$this->m_dbColumnName."'";
			$row = $Campsite['db']->GetAll($queryStr);
			if (!is_null($row) && is_array($row) && sizeof($row) > 0 && !is_null($row[0])) {
				$this->fetch($row[0]);
			}
		}
	} // fn fetch


	function delete()
	{
		global $Campsite;
		$queryStr = "ALTER TABLE ".$this->m_dbTableName." DROP COLUMN ".$this->m_dbColumnName;
		$success = $Campsite['db']->Execute($queryStr);
		if ($success) {
			$queryStr = "DELETE FROM TopicFields WHERE ArticleType = '".$this->m_articleTypeName
						."' and FieldName = '".substr($this->m_dbColumnName, 1)."'";
			$Campsite['db']->Execute($queryStr);
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Article type field $1 deleted', $this->m_dbColumnName);
			Log::Message($logtext, null, 72);
			ParserCom::SendMessage("article_types", "modify", array("article_type"=>"$AType"));
		}
	} // fn delete


	/**
	 * @return string
	 */
	function getName()
	{
		return $this->Field;
	} // fn getName


	/**
	 * @return string
	 */
	function getPrintName()
	{
		return substr($this->Field, 1);
	} // fn getPrintName


	/**
	 * @return string
	 */
	function getType()
	{
		global $Campsite;
		if (stristr($this->Type, 'int') != '') {
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    					.$this->m_articleTypeName."' and FieldName = '"
    					.substr($this->Field, 1)."'";
    		$topicId = $Campsite['db']->GetOne($queryStr);
    		if ($topicId > 0) {
				return 'topic';
    		}
		}
		return strtolower($this->Type);
	} // fn getType


	/**
	 * @return string
	 */
	function getTopicTypeRootElement()
	{
		global $Campsite;
		$topicId = null;
		if (stristr($this->Type, 'int') != '') {
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    					.$this->m_articleTypeName."' and FieldName = '"
    					.substr($this->Field, 1)."'";
    		$topicId = $Campsite['db']->GetOne($queryStr);
		}
		return $topicId;
	}


	/**
	 * Get a human-readable representation of the column type.
	 * @return string
	 */
	function getPrintType($p_languageId = 1)
	{
		global $Campsite;
		switch ($this->getType()) {
	    case 'mediumblob':
	    	return getGS('Article body');
	    case 'varchar(255)':
	    	return getGS('Text');
	    case 'varbinary(255)':
	    	return getGS('Text');
	    case 'date':
	    	return getGS('Date');
	    case 'topic':
    		$queryStr = "SELECT RootTopicId FROM TopicFields WHERE ArticleType = '"
    					.$this->m_articleTypeName."' and FieldName = '"
    					.substr($this->Field, 1)."'";
    		$topicId = $Campsite['db']->GetOne($queryStr);
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

	function getDisplayName() {
		global $_REQUEST;
		$loginLanguageId = 0;
		$loginLanguage = Language::GetLanguages(null, $_REQUEST['TOL_Language']);
		if (is_array($loginLanguage)) {
			$loginLanguage = array_pop($loginLanguage);
			$loginLanguageId = $loginLanguage->getLanguageId();
		}
		$translations = $this->getTranslations();
		if (!isset($translations[$loginLanguageId])) return $this->getPrintName();
		else return $translations[$loginLanguageId] .' ('. $loginLanguage->getCode() .')';		

	}


	function setStatus($p_status) {
		global $Campsite;
		if ($p_status == 'show') $set = "is_hidden=0";
		if ($p_status == 'hide') $set = "is_hidden=1";
		$queryStr = "UPDATE ArticleTypeMetadata SET $set WHERE type_name='". $this->m_dbTableName ."' AND field_name='". $this->Field ."'";
		$ret = $Campsite['db']->Execute($queryStr);
	}

	/** 
	* Return an associative array of the metadata in ArticleFieldMetadata.
	*
	**/
	function getMetadata() {
		global $Campsite;
		$queryStr = "SELECT * FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' and field_name='". $this->Field ."'";
		$queryArray = $Campsite['db']->GetAll($queryStr);
		return $queryArray;
	}

	function getTranslations() {
		$return = array();
		foreach ($this->m_metadata as $m) {
			if (is_numeric($m['fk_phrase_id'])) {
				$tmp = Translation::getTranslations($m['fk_phrase_id']);
				foreach ($tmp as $k => $v) 
					$return[$k] = $v;
				unset($tmp);
			}
		}	
		return $return;
	}

	
	/**
	 * Set the field name for the given language.  A new entry in 
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
			$phase_id = $this->m_metadata['fk_phrase_id'];
			$trans =& new Translation($p_languageId, $phrase_id);
			$trans->delete();
			$sql = "DELETE FROM ArticleTypeMetadata WHERE type_name=". $this->m_dbTableName ." AND field_name=". $this->m_dbColumnName ." AND fk_phrase_id=". $phrase_id;
			$changed = $Campsite['db']->Execute($sql);
		}
		
		if (isset($this->m_names[$p_languageId])) {
			$description =& new Translation($p_languageId, $this->m_metadata['fk_phrase_id']);
			$description->setText($p_value);
			
			// Update the name.
			$oldValue = $this->m_names[$p_languageId];
			$changed = true;
		} else {
			// Insert the new translation.
			$description =& new Translation($p_languageId);
			$description->create($p_value);
			$phrase_id = $description->getPhraseId();

			$oldValue = "";
			$sql = "INSERT INTO ArticleTypeMetadata SET type_name='".$this->m_dbTableName ."', field_name='". $this->m_dbColumnName ."', fk_phrase_id=".$phrase_id;
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

	function getOrders() {
		global $Campsite;
		$queryStr = "SELECT field_weight FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' ORDER BY field_weight DESC LIMIT 1,1";
		$max = $Campsite['db']->getOne($queryStr);
		if ($max == NULL) $max = 0;
		$queryStr = "SELECT field_weight, field_name FROM ArticleTypeMetadata WHERE type_name='". $this->m_dbTableName ."' AND field_name IS NOT NULL";
		$queryArray = $Campsite['db']->GetAll($queryStr);
		$orderArray = array();
		foreach ($queryArray as $row => $values) {
			if ($values['field_weight'] == NULL) { $values['field_weight'] = $max++; }
			$orderArray[$values['field_weight']] = $values['field_name'];
		}
		return $orderArray;	
	}

	function setOrders($orderArray) {
		global $Campsite;
		foreach ($orderArray as $order => $field) {
			$queryStr = "UPDATE ArticleTypeMetadata SET field_weight=$order WHERE type_name='". $this->m_dbTableName ."' AND field_name='". $field ."'";
			$Campsite['db']->Execute($queryStr);
		}
	}

	function reorder($move) {
		$orders = $this->getOrders();
		$tmp = array_keys($orders, $this->Field);
		$pos = $tmp[0];
		if ($pos == 0 && $move == 'down') return;
		if ($pos == count($orders) && $move == 'up') return;
		if ($move == 'down') {
			$tmp = $orders[$pos - 1];
			$orders[$pos - 1] = $orders[$pos];
			$orders[$pos] = $tmp;
		}		
		if ($move == 'up') {
			$tmp = $orders[$pos + 1];
			$orders[$pos + 1] = $orders[$pos];
			$orders[$pos] = $tmp;
		}
		$this->setOrders($orders);	
	}

} // class ArticleTypeField

?>