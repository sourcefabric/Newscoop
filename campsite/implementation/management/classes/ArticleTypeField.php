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
			$queryStr = "INSERT INTO ArticleTypeMetadata (type_name, field_name, field_type) VALUES ('". $this->m_dbTableName ."','". $this->m_dbColumnName ."', '". $p_type ."')";
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
		return "NAME";
	}


	function hide() {
		global $Campsite;
		$queryStr = "UPDATE ArticleTypeMetadata SET is_hidden=1 WHERE type_name='". $this->m_dbTableName ."' AND field_name='". $this->m_fieldName ."'";
		$ret = $Campsite['db']->Execute($queryStr);
	
	}
	
	function show() {
		global $Campsite;
		$queryStr = "UPDATE ArticleTypeMetadata SET is_hidden=0 WHERE type_name='". $this->m_dbTableName ."' AND field_name='". $this->m_fieldName ."'";
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

} // class ArticleTypeField

?>