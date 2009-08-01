<?php
/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class Topic extends DatabaseObject {
	var $m_keyColumnNames = array('Id');

	var $m_dbTableName = 'Topics';

	var $m_columnNames = array('Id', 'LanguageId', 'Name', 'ParentId');

	var $m_hasSubtopics = null;

	var $m_names = array();


	/**
	 * A topic is like a category for a piece of data.
	 *
	 * @param int $p_id
	 */
	public function Topic($p_idOrName = null)
	{
		parent::DatabaseObject($this->m_columnNames);

		if (preg_match('/[\d]+/', $p_idOrName) > 0) {
            $this->m_data['Id'] = $p_idOrName;
            $this->fetch();
		} elseif (is_string($p_idOrName) && !empty($p_idOrName)) {
		    $topic = Topic::GetByFullName($p_idOrName);
		    if (!is_null($topic)) {
		        $this->fetch($topic->m_data);
		    }
		}
	} // constructor


	/**
	 * Fetch the topic and all its translations.
	 *
	 * @return void
	 */
	public function fetch($p_columns = null)
	{
		global $g_ado_db;
		if (!is_null($p_columns)) {
			foreach ($p_columns as $columnName => $value) {
				if (in_array($columnName, $this->m_columnNames)) {
					$this->m_data[$columnName]  = $value;
				}
			}
			if (isset($p_columns['LanguageId']) && isset($p_columns['Name'])) {
				$this->m_names[$p_columns['LanguageId']] = $p_columns['Name'];
			}
			$this->m_exists = true;
		} else {
			$columnNames = implode(",", $this->m_columnNames);
			$queryStr = "SELECT $columnNames FROM ".$this->m_dbTableName
						." WHERE Id=".$this->m_data['Id'];
			$rows = $g_ado_db->GetAll($queryStr);
			if ($rows && (count($rows) > 0)) {
				$row = array_pop($rows);
				$this->m_data['Id'] = $row['Id'];
				$this->m_data['ParentId'] = $row['ParentId'];
				$this->m_names[$row['LanguageId']] = $row['Name'];
				foreach ($rows as $row) {
					$this->m_names[$row['LanguageId']] = $row['Name'];
				}
				$this->m_exists = true;
			}
		}
	} // fn fetch


	/**
	 * Create a new topic.
	 *
	 * @param array $p_values
	 * @return boolean
	 */
	public function create($p_values = null)
	{
		global $g_ado_db;
		$queryStr = "UPDATE AutoId SET TopicId = LAST_INSERT_ID(TopicId + 1)";
		$g_ado_db->Execute($queryStr);
		$this->m_data['Id'] = $g_ado_db->Insert_ID();
		$this->m_data['LanguageId'] = 1;
		if (isset($p_values['LanguageId'])) {
			$this->m_data['LanguageId'] = $p_values['LanguageId'];
		}
		$this->m_data['Name'] = "";
		if (isset($p_values['Name'])) {
			$this->m_names[$this->m_data['LanguageId']] = $p_values['Name'];
		}
		$success = parent::create($p_values);
		if ($success) {
			$this->m_exists = true;
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Topic "$1" ($2) added', $this->m_data['Name'], $this->m_data['Id']);
			Log::Message($logtext, null, 141);
		}
		return $success;
	} // fn create


	/**
	 * Delete the topic.
	 * @return boolean
	 */
	public function delete($p_languageId = null)
	{
		global $g_ado_db;
		$deleted = false;
		if (is_null($p_languageId)) {
			// Delete all translations
			$sql = "DELETE FROM Topics WHERE Id=".$this->m_data['Id'];
			$deleted = $g_ado_db->Execute($sql);
		} elseif (is_numeric($p_languageId)) {
			// Delete specific translation
			$sql = "DELETE FROM Topics WHERE Id=".$this->m_data['Id']." AND LanguageId=".$p_languageId;
			$deleted = $g_ado_db->Execute($sql);
		}

		// Delete the ATF metadata
        if ($deleted) {

            $sql = "SELECT * FROM TopicFields WHERE RootTopicId=". $this->m_data['Id'];

            $row = $g_ado_db->GetRow($sql);
            if ($row) {
                $delATF = new ArticleTypeField($row['ArticleType'], $row['FieldName']);
                $deleted = $delATF->delete();
            }
        }

		if ($deleted) {
			$this->m_exists = false;
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			if (is_null($p_languageId)) {
				$name = implode(",", $this->m_names);
			} else {
				$name = $this->m_names[$p_languageId];
			}
			$logtext = getGS('Topic "$1" ($2) deleted', $name, $this->m_data['Id']);
			Log::Message($logtext, null, 142);
		}
		return $deleted;
	} // fn delete


	/**
	 * @return string
	 */
	public function getName($p_languageId)
	{
		if (is_numeric($p_languageId) && isset($this->m_names[$p_languageId])) {
			return $this->m_names[$p_languageId];;
		} else {
			return "";
		}
	} // fn getName


	/**
	 * Set the topic name for the given language.  A new entry in
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
		if (!is_string($p_value) || !is_numeric($p_languageId)) {
			return false;
		}

		if (isset($this->m_names[$p_languageId])) {
			// Update the name.
			$oldValue = $this->m_names[$p_languageId];
			$sql = "UPDATE Topics SET Name='".mysql_real_escape_string($p_value)."' "
					." WHERE Id=".$this->m_data['Id']
					." AND LanguageId=".$p_languageId;
			$changed = $g_ado_db->Execute($sql);
		} else {
			// Insert the new translation.
			$oldValue = "";
			$sql = "INSERT INTO Topics SET Name='".mysql_real_escape_string($p_value)."' "
					.", Id=".$this->m_data['Id']
					.", LanguageId=$p_languageId"
					.", ParentId=".$this->m_data['ParentId'];
			$changed = $g_ado_db->Execute($sql);
		}
		if ($changed) {
			$this->m_names[$p_languageId] = $p_value;
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Topic $1: ("$2" -> "$3") updated', $this->m_data['Id'], $oldValue, $this->m_names[$p_languageId]);
			Log::Message($logtext, null, 143);
		}
		return $changed;
	} // fn setName


	/**
	 * @return int
	 */
	public function getTopicId()
	{
		return $this->m_data['Id'];
	} // fn getTopicId


	/**
	 * Get all translations of the topic in an array indexed by
	 * the language ID.
	 *
	 * @return array
	 */
	public function getTranslations()
	{
	    return $this->m_names;
	} // fn getTranslations


	/**
	 * Return the number of translations of this topic.
	 *
	 * @return int
	 */
	public function getNumTranslations()
	{
		return count($this->m_names);
	} // fn getNumTranslations


	/**
	 * @return int
	 */
	public function getParentId()
	{
		return $this->m_data['ParentId'];
	} // fn getParentId


	/**
	 * Return an array of Topics starting from the root down
	 * to and including the current topic.
	 *
	 * @return array
	 */
	public function getPath()
	{
		global $g_ado_db;
		$done = false;
		$currentId = $this->m_data['Id'];
		$stack = array();
		while (!$done) {
			$queryStr = 'SELECT * FROM Topics WHERE Id = '.$currentId;
			$rows = $g_ado_db->GetAll($queryStr);
			if (($rows !== false) && (count($rows) > 0)) {
				$row = array_pop($rows);
				$topic = new Topic();
				$topic->fetch($row);
				// Get all the translations
				foreach ($rows as $row) {
					$topic->m_names[$row['LanguageId']] = $row['Name'];
				}
				array_unshift($stack, $topic);
				$currentId = $topic->getParentId();
			} else {
				$done = true;
			}
		}
		return $stack;
	} // fn getPath


    public function isRoot()
    {
        return $this->m_data['ParentId'] == 0;
    }

	/**
	 * Return true if this topic has subtopics.
	 *
	 * @return boolean
	 */
	public function hasSubtopics()
	{
		global $g_ado_db;
		// Returned the cached value if available.
		if (!is_null($this->m_hasSubtopics)) {
			return $this->m_hasSubtopics;
		}
		$queryStr = 'SELECT COUNT(*) FROM Topics WHERE ParentId = '.$this->m_data['Id'];
		$numRows = $g_ado_db->GetOne($queryStr);
		return ($numRows > 0);
	} // fn hasSubtopics


	/**
	 * Returns a topic object identified by the full name in the
	 * format topic_name:language_code
	 *
	 * @param string $p_fullName
	 * @return Topic object
	 */
	public static function GetByFullName($p_fullName)
	{
	    $components = preg_split('/:/', trim($p_fullName));
	    if (count($components) < 2) {
	        return null;
	    }
	    $name = $components[0];
	    $languageCode = $components[1];

	    $languages = Language::GetLanguages(null, $languageCode, null, array(), array(), true);
	    if (count($languages) < 1) {
	        return null;
	    }
        $languageObject = $languages[0];

        $topics = Topic::GetTopics(null, $languageObject->getLanguageId(), $name);
	    if (count($topics) < 1) {
	        return null;
	    }

	    return $topics[0];
	}


	/**
	 * Search the Topics table.
	 *
	 * @param int $p_id
	 * @param int $p_languageId
	 * @param string $p_name
	 * @param int $p_parentId
	 * @param array $p_sqlOptions
	 * @return array
	 */
	public static function GetTopics($p_id = null, $p_languageId = null, $p_name = null,
					                 $p_parentId = null, $p_sqlOptions = null, $p_order = null)
	{
		$constraints = array();
		if (!is_null($p_id)) {
			$constraints[] = array("Id", $p_id);
		}
		if (!is_null($p_languageId)) {
			$constraints[] = array("LanguageId", $p_languageId);
		}
		if (!is_null($p_name)) {
			$constraints[] = array("Name", $p_name);
		}
		if (!is_null($p_parentId)) {
			$constraints[] = array("ParentId", $p_parentId);
		}
		if (is_array($p_order) && count($p_order) > 0) {
			$order = array();
			foreach ($p_order as $orderCond) {
				switch ($orderCond['field']) {
                	case 'byname':
                		$order['Name'] = $orderCond['dir'];
                		break;
                	case 'bynumber':
                		$order['Id'] = $orderCond['dir'];
                		break;
                }
			}
			$p_sqlOptions['ORDER BY'] = $order;
		}
		return DatabaseObject::Search('Topic', $constraints, $p_sqlOptions);
	} // fn GetTopics
	
	
	public function getSubtopics($p_returnIds = false)
	{
        global $g_ado_db;

		$sql = "SELECT DISTINCT Id FROM Topics WHERE ParentId = " . (int)$this->m_data['Id'];
		$rows = $g_ado_db->GetAll($sql);
		$topics = array();
		foreach ($rows as $row) {
			$topics[] = $p_returnIds ? $row['Id'] : new Topic($row['Id']);
		}
		return $topics;
	}


	/**
	 * Traverse the tree from the given topic ID.
	 *
	 * @param array $p_tree
	 * @param array $p_path
	 * @param int $p_topicId
	 */
	private static function __TraverseTree(&$p_tree, $p_path, $p_topicId = 0)
	{
		global $g_ado_db;
		$sql = "SELECT * FROM Topics WHERE ParentId = ".$p_topicId
				." ORDER BY Id ASC, LanguageId ASC, Name ASC ";
		$rows = $g_ado_db->GetAll($sql);
		if ($rows) {
			$previousTopic = new Topic();

			$currentTopics = array();

			// Get all the topics at the current level of the tree.
			// Translations of a topic are merged into a single topic.
			foreach ($rows as $row) {
				// If its a translation of the previous topic, add it as a translation.
				if ($previousTopic->m_data['Id'] == $row['Id']){
					$previousTopic->m_names[$row['LanguageId']] = $row['Name'];
				} else {
					// This is a new topic, not a translation.
					$currentTopics[$row['Id']] = new Topic();
					$currentTopics[$row['Id']]->fetch($row);

					// Remember this topic so we know if the next topic
					// is a translation of this one.
					$previousTopic =& $currentTopics[$row['Id']];

					// Create the entry in the tree for the current topic.

					// Copy the current path.  We need to make a copy
					// because if we added to $p_path, it would get longer
					// each time around the loop.
					$newPath = $p_path;

					// Add the current topic to the path.
					$newPath[$row['Id']] =& $currentTopics[$row['Id']];

					// Add the path to the tree.
					$p_tree[] = $newPath;

					// Descend the tree - dont worry, the translations will be added
					// the next time around the loop.
					Topic::__TraverseTree($p_tree, $newPath, $row['Id']);
				}
			} // foreach

		}
	} // fn __TraverseTree


	/**
	 * Get all the topics in an array, where each element contains the entire
	 * path for each topic.  Each topic will be indexed by its ID.
	 * For example, if we have the following topic structure (IDs are
	 * in brackets):
	 *
	 * sports (1)
	 *  - baseball (2)
	 *  - soccer (3)
	 *    - player stats (4)
	 *    - matches (5)
	 * politics (6)
	 *  - world (7)
	 *  - local (8)
	 *
	 *  ...then the returned array would look like:
	 *  array(array(1 => "sports"),
	 *        array(1 => "sports", 2 => "baseball"),
	 *        array(1 => "sports", 3 => "soccer"),
	 *        array(1 => "sports", 3 => "soccer", 4 => "player stats"),
	 *        array(1 => "sports", 3 => "soccer", 5 => "matches"),
	 *        array(6 => "politics"),
	 *        array(6 => "politics", 7 => "world"),
	 *        array(6 => "politics", 8 => "local")
	 *  );
	 *
	 * @param int $p_startingTopicId
	 * @return array
	 */
	public static function GetTree($p_startingTopicId = 0)
	{
		$tree = array();
		$path = array();
		Topic::__TraverseTree($tree, $path, $p_startingTopicId);
		return $tree;
	} // fn GetTree

} // class Topics

?>