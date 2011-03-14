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
	var $m_keyColumnNames = array('id');

	var $m_keyIsAutoIncrement = true;

	var $m_dbTableName = 'Topics';

	var $m_columnNames = array('id', 'node_left', 'node_right');

	var $m_names = array();


	/**
	 * A topic is like a category for a piece of data.
	 *
	 * @param int $p_id
	 */
	public function Topic($p_idOrName = null)
	{
		parent::DatabaseObject($this->m_columnNames);

		if (preg_match('/^[\d]+$/', $p_idOrName) > 0) {
            $this->m_data['id'] = $p_idOrName;
            $this->fetch();
		} elseif (is_string($p_idOrName) && !empty($p_idOrName)) {
            $topic = Topic::GetByFullName($p_idOrName);
            if (!is_null($topic)) {
		        $this->duplicateObject($topic);
		    }
		}
	} // constructor


	/**
	 * Fetch the topic and all its translations.
	 *
	 * The values array may have the following keys:
	 * - id - topic identifier; if not supplied generated automatically
	 * - node_left
	 * - node_right
	 * - names - array of topic translations of the form: language_id => name
	 *
	 * @return void
	 */
	public function fetch($p_columns = null)
	{
		global $g_ado_db;

		if (!is_null($p_columns)) {
			if (!isset($p_columns['names'])) {
				return false;
			}
			if ($this->readFromCache($p_columns) !== false) {
                return true;
            }
			foreach ($p_columns as $columnName => $value) {
				if (in_array($columnName, $this->m_columnNames)) {
					$this->m_data[$columnName]  = $value;
				}
			}
			$this->m_names = $p_columns['names'];
			$this->m_exists = true;
		} else {
            if ($this->readFromCache() !== false) {
                return true;
            }
            parent::fetch();
            if ($this->exists()) {
				$this->m_names = TopicName::GetTopicNames($this->getTopicId());
            }
		}

		if ($this->m_exists) {
		    // Write the object to cache
		    $this->writeCache();
		}

        return $this->m_exists;
	} // fn fetch


	/**
	 * Create a new topic.
	 *
	 * The values array may have the following keys:
	 * - parent_id - parent topic identifier
	 * - id - topic identifier; if not supplied generated automatically
	 * - node_left
	 * - node_right
	 * - names - array of topic translations of the form: language_id => name
	 *
	 * @param array $p_values
	 * @return boolean
	 */
	public function create($p_values = null)
	{
		global $g_ado_db;

		if (!isset($p_values['names'])) {
			return false;
		}

		$g_ado_db->Execute("LOCK TABLE Topics WRITE, TopicNames WRITE");

		if (isset($p_values['parent_id']) && !empty($p_values['parent_id'])) {
			$parent = new Topic($p_values['parent_id']);
			if (!$parent->exists()) {
				$g_ado_db->Execute("UNLOCK TABLES");
				return false;
			}
			$parentLeft = (int)$parent->getLeft();
		} else {
			$parentLeft = 0;
		}

		$g_ado_db->Execute("UPDATE Topics SET node_left = node_left + 2 WHERE node_left > $parentLeft");
		$g_ado_db->Execute("UPDATE Topics SET node_right = node_right + 2 WHERE node_right > $parentLeft");

		$this->m_data['node_left'] = $parentLeft + 1;
		$this->m_data['node_right'] = $parentLeft + 2;

		// create node
		if ($success = parent::create($p_values)) {
			// create topic names
			foreach ($p_values['names'] as $languageId=>$name) {
				$topicName = new TopicName($this->getTopicId(), $languageId);
				$topicName->create(array('name'=>$name));
				$this->m_names[$languageId] = $topicName;
			}
		}

		$g_ado_db->Execute("UNLOCK TABLES");

		if ($success) {
			$this->m_exists = true;
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Topic "$1" ($2) added', implode(', ', $this->m_names), $this->m_data['id']);
			Log::Message($logtext, null, 141);
		}
		CampCache::singleton()->clear('user');
		return $success;
	} // fn create


	/**
	 * Returns the node left order
	 * @param bool $p_forceFetchFromDatabase
	 * @return integer
	 */
	public function getLeft($p_forceFetchFromDatabase = false)
	{
		return (int)$this->getProperty('node_left', $p_forceFetchFromDatabase);
	} // fn getLeft


	/**
	 * Returns the node right order
	 * @param bool $p_forceFetchFromDatabase
	 * @return integer
	 */
	public function getRight($p_forceFetchFromDatabase = false)
	{
		return (int)$this->getProperty('node_right', $p_forceFetchFromDatabase);
	} // fn getRight


	/**
	 * Returns the node right width: right order - left order
	 * @param bool $p_forceFetchFromDatabase
	 * @return integer
	 */
	public function getWidth($p_forceFetchFromDatabase = false)
	{
		return $this->getRight() - $this->getLeft();
	} // fn getWidth


	/**
	 * Set the given column name to the given value.
	 * The object's internal variable will also be updated.
	 * If the value hasn't changed, the database will not be updated.
	 *
	 * Note: Returns false when setting the fields node_left and node_right.
	 * We don't want to allow direct update of these fields.
	 *
	 * Note: You cannot set $p_commit to FALSE and $p_isSql to TRUE
	 * at the same time.
	 *
	 * @param string $p_dbColumnName
	 *		The name of the column that is to be updated.
	 *
	 * @param string $p_value
	 *		The value to set.
	 *
	 * @param boolean $p_commit
	 *		If set to true, the value will be written to the database immediately.
	 *		If set to false, the value will not be written to the database.
	 *		Default is true.
	 *
	 * @param boolean $p_isSql
	 *		Set this to TRUE if p_value consists of SQL commands.
	 *		There is no way to know what the result of the command is,
	 *		so we will need to refetch the value from the database in
	 *		order to update the internal variable's value.
	 *
	 * @return boolean
	 *		TRUE on success, FALSE on error.
	 */
	public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
	{
		if ($p_dbColumnName == 'node_left' || $p_dbColumnName == 'node_right') {
			return false;
		}
		return parent::setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql);
	} // fn setProperty


	/**
	 * Delete the topic.
	 * @return boolean
	 */
	public function delete($p_languageId = null)
	{
		global $g_ado_db;

		$g_ado_db->Execute("LOCK TABLE Topics WRITE, TopicNames WRITE, TopicFields READ, ArticleTypeMetadata WRITE");

		$topicId = $this->getTopicId();
		if ($p_languageId > 0 && $this->getNumTranslations() > 1) {
			$deletedName = $this->m_names[$p_languageId];
			$topicName = new TopicName($this->getTopicId(), $p_languageId);
			$deleted = $topicName->delete();
			if ($deleted) {
				unset($this->m_names[$p_languageId]);
			}
		} else {
			$deletedName = implode(",", $this->m_names);

			// Delete the article type field metadata
			$sql = "SELECT * FROM TopicFields WHERE RootTopicId IN "
			."(SELECT DISTINCT Id FROM Topics WHERE node_left >= ". $this->m_data['node_left']
			." AND node_right < " . $this->m_data['node_right'] . ")";
			$rows = $g_ado_db->GetAll($sql);
			foreach ($rows as $row) {
				$delATF = new ArticleTypeField($row['ArticleType'], $row['FieldName']);
				$delATF->delete();
			}

			// Delete topic names
			TopicName::DeleteTopicNames($this->getTopicId());

			// Delete children and itself
			$sql = "DELETE FROM Topics WHERE node_left >= ".$this->m_data['node_left']
			. ' AND node_right <= '.$this->m_data['node_right'];
			$deleted = $g_ado_db->Execute($sql);

			if ($deleted) {
				$myWidth = $this->m_data['node_right'] - $this->m_data['node_left'] + 1;
				$sql = "UPDATE Topics SET node_left = node_left - $myWidth WHERE node_left > " . $this->m_data['node_left'];
				$g_ado_db->Execute($sql);
				$sql = "UPDATE Topics SET node_right = node_right - $myWidth WHERE node_right > " . $this->m_data['node_right'];
				$g_ado_db->Execute($sql);
			}

			$this->m_data = array();
			$this->m_exists = false;
		}

		$g_ado_db->Execute("UNLOCK TABLES");

		if ($deleted) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Topic "$1" ($2) deleted', $deletedName, $topicId);
			Log::Message($logtext, null, 142);
		}
		CampCache::singleton()->clear('user');
		return $deleted;
	} // fn delete


	/**
	 * @return string
	 */
	public function getName($p_languageId)
	{
		if (is_numeric($p_languageId) && isset($this->m_names[$p_languageId])) {
			return $this->m_names[$p_languageId]->getName();
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
			$oldName = $this->m_names[$p_languageId]->getName();
			$changed = $this->m_names[$p_languageId]->setName($p_value);
		} else {
			$topicName = new TopicName($this->getTopicId(), $p_languageId);
			$changed = $topicName->create(array('name'=>$p_value));
			$this->m_names[$p_languageId] = $topicName;
		}
		if ($changed) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			if (!empty($oldName)) {
				$logtext = getGS('Topic $1: ("$2" -> "$3") updated', $this->m_data['id'], $oldName, $this->m_names[$p_languageId]);
			} else {
				$logtext = getGS('Topic "$1" ($2) added', $this->m_names[$p_languageId], $this->m_data['id']);
			}
			Log::Message($logtext, null, 143);
		}
		return $changed;
	} // fn setName


	/**
	 * @return int
	 */
	public function getTopicId()
	{
		return $this->m_data['id'];
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
		global $g_ado_db;

		if (!$this->exists()) {
			return null;
		}

		$sql = 'SELECT id FROM Topics WHERE node_left < ' . $this->getLeft()
		. ' AND node_right > ' . $this->getRight() . ' ORDER BY Id DESC';
		$parentId = $g_ado_db->GetOne($sql);
		return $parentId;
	} // fn getParentId


	/**
	 * Return an array of Topics starting from the root down
	 * to and including the current topic.
	 *
	 * @return array
	 */
	public function getPath($p_returnIds = false)
	{
		global $g_ado_db;

		if (!$this->exists()) {
			return array();
		}

		$stack = array();
		$sql = 'SELECT * FROM Topics WHERE node_left <= ' . $this->getLeft()
		. ' AND node_right >= ' . $this->getRight() . ' ORDER BY node_left ASC';
		$rows = $g_ado_db->GetAll($sql);
		foreach ($rows as $row) {
			$stack[$row['id']] = $p_returnIds ? $row['id'] : new Topic($row['id']);
		}
		return $stack;
	} // fn getPath


	/**
	 * Returns true if it was a root topic
	 * @return boolean
	 */
    public function isRoot()
    {
    	global $g_ado_db;

		if (!$this->exists()) {
			return null;
		}

    	$sql = 'SELECT COUNT(*) FROM Topics WHERE node_left < ' . $this->getLeft()
    	. ' AND node_right > ' . $this->getRight();
    	$parentsCount = $g_ado_db->GetOne($sql);
    	return $parentsCount == 0;
    } // fn isRoot


	/**
	 * Return true if this topic has subtopics.
	 *
	 * @return boolean
	 */
	public function hasSubtopics()
	{
		return ($this->getRight() - $this->getLeft()) > 1;
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

	    $languages = Language::GetLanguages(null, $languageCode, null, array(), array(), false);
	    if (count($languages) < 1) {
	        return null;
	    }
        $languageObject = $languages[0];

        $topics = Topic::GetTopics(null, $languageObject->getLanguageId(), $name);
	    if (count($topics) < 1) {
	        return null;
	    }

	    return $topics[0];
	} // fn GetByFullName


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
					                 $p_parentId = null, $p_depth = 1, $p_sqlOptions = null,
					                 $p_order = null, $p_countOnly = false, $p_skipCache = false)
	{
        global $g_ado_db;
		if (!$p_skipCache && CampCache::IsEnabled()) {
            $paramsArray['id'] = (is_null($p_id)) ? '' : $p_id;
            $paramsArray['language_id'] = (is_null($p_languageId)) ? '' : $p_languageId;
            $paramsArray['name'] = (is_null($p_name)) ? '' : $p_name;
            $paramsArray['parent_id'] = (is_null($p_parentId)) ? '' : $p_parentId;
            $paramsArray['depth'] = (is_null($p_depth)) ? '' : $p_depth;
            $paramsArray['sql_options'] = $p_sqlOptions;
            $paramsArray['order'] = $p_order;
            $paramsArray['count_only'] = (int)$p_countOnly;
            $cacheListObj = new CampCacheList($paramsArray, __METHOD__);
            $topics = $cacheListObj->fetchFromCache();
            if ($topics !== false && is_array($topics)) {
                return $p_countOnly ? $topics['count'] : $topics;
            }
        }

		if (!is_array($p_order) || count($p_order) == 0) {
			$p_order = array(array('field'=>'default', 'dir'=>'asc'));
		}
		foreach ($p_order as $orderCond) {
			switch (strtolower($orderCond['field'])) {
				case 'default':
					$order['t.node_left'] = $orderCond['dir'];
					break;
				case 'byname':
					$order['tn.name'] = $orderCond['dir'];
					break;
				case 'bynumber':
					$order['t.id'] = $orderCond['dir'];
					break;
			}
		}
		$p_sqlOptions['ORDER BY'] = $order;

		$query = new SQLSelectClause();
		$query->addColumn('t.id');
		$topicObj = new Topic();
		$topicNameObj = new TopicName();
		if ((!is_null($p_languageId) && is_numeric($p_languageId))
		|| !is_null($p_name) || isset($order['tn.name'])) {
			$query->setTable($topicObj->m_dbTableName . ' AS t LEFT JOIN '
			. $topicNameObj->m_dbTableName . ' AS tn ON t.id = tn.fk_topic_id');
		} else {
			$query->setTable($topicObj->m_dbTableName . ' AS t');
		}

        $constraints = array();
		if (!is_null($p_id) && is_numeric($p_id)) {
			$query->addWhere("t.id = '$p_id'");
		}
		if (!is_null($p_languageId) && is_numeric($p_languageId)) {
			$query->addWhere("tn.fk_language_id = '$p_languageId'");
		}
		if (!is_null($p_name)) {
			$query->addWhere("tn.name = '". $g_ado_db->escape($p_name) . "'");
		}
		if (!is_null($p_parentId)) {
			$subtopicsQuery = self::BuildSubtopicsQuery($p_parentId, $p_depth, 1);
			$query->addTableFrom('(' . $subtopicsQuery->buildQuery() . ') AS in_query');
			$query->addWhere("t.id = in_query.id");
		}

		$queryStr = $query->buildQuery();
        $queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
        if ($p_countOnly) {
        	$queryStr = "SELECT COUNT(*) FROM ($queryStr) AS topics";
        	$topics['count'] = $g_ado_db->GetOne($queryStr);
        } else {
        	$topics = array();
        	$rows = $g_ado_db->GetAll($queryStr);
        	foreach ($rows as $row) {
        		$topics[] = new Topic($row['id']);
        	}
        }

        if (!$p_skipCache && CampCache::IsEnabled()) {
            $cacheListObj->storeInCache($topics);
        }

        return $topics;
	} // fn GetTopics


	/**
	 * Returns the subtopics from the next level (not all levels below) in an array
	 * of topic identifiers.
	 * @param array $p_returnIds
	 * @param integer $p_depth
	 */
	public function getSubtopics($p_returnIds = false, $p_depth = 1)
	{
        global $g_ado_db;

        $parentDepthQuery = self::BuildSubtopicsQuery($this->getTopicId(), $p_depth);
		$rows = $g_ado_db->GetAll($parentDepthQuery->buildQuery());
		$topics = array();
		foreach ($rows as $row) {
			$topics[] = $p_returnIds ? $row['id'] : new Topic($row['id']);
		}
		return $topics;
	} // getSubtopics


	/**
	 * Returns an SQLSelectClause object that builds a query for retrieving the
	 * depth of the given topic.
	 *
	 * @param integer $p_topicId - topic identifier
	 * @param integer $p_indent - query formatting: indent the query $p_indent times
	 * @return SQLSelectClause
	 */
	public static function BuildDepthQuery($p_topicId, $p_indent = 0)
	{
		$topicObj = new Topic();

        $depthQuery = new SQLSelectClause($p_indent);
        $depthQuery->setTable($topicObj->m_dbTableName . ' as node');
        $depthQuery->addTableFrom($topicObj->m_dbTableName . ' as parent');
        $depthQuery->addColumn('node.id');
        $depthQuery->addColumn('(COUNT(parent.id) - 1) AS depth');
        $depthQuery->addWhere('node.node_left BETWEEN parent.node_left AND parent.node_right');
        $depthQuery->addWhere('node.id = ' . (int)$p_topicId);
        $depthQuery->addGroupField('node.id');
        $depthQuery->addOrderBy('node.node_left');
        return $depthQuery;
	}


	/**
	 * Returns an SQLSelectClause object that builds a query for retrieving the
	 * subtopics of the given parent.
	 *
	 * @param integer $p_parentId - parent topic identifier
	 * @param integer $p_depth - depth of the subtopic tree; default 1; 0 for unlimitted
	 * @param integer $p_indent - query formatting: indent the query $p_indent times
	 * @return SQLSelectClause
	 */
	public static function BuildSubtopicsQuery($p_parentId = 0, $p_depth = 1, $p_indent = 0)
	{
		$topicObj = new Topic();

		$depthGreater = $p_parentId > 0 ? 'depth > 0' : 'depth >= 0';
		$depthMax = $p_parentId > 0 ? (int)$p_depth : $p_depth - 1;

		$query = new SQLSelectClause($p_indent);
		$query->addColumn('node.id');
		$query->setTable($topicObj->m_dbTableName . ' as node');
        $query->addTableFrom($topicObj->m_dbTableName . ' as parent');
        if ($p_parentId > 0) {
        	$query->addColumn('(COUNT(parent.id) - (sub_tree.depth + 1)) AS depth');
        	$query->addTableFrom($topicObj->m_dbTableName . ' as sub_parent');
        	$parentDepthQuery = self::BuildDepthQuery($p_parentId, $p_indent+1);
        	$query->addTableFrom('(' . $parentDepthQuery->buildQuery() . ') as sub_tree');
        	$query->addWhere('sub_parent.id = sub_tree.id');
        	$query->addWhere('node.node_left BETWEEN sub_parent.node_left AND sub_parent.node_right');
        } else {
        	$query->addColumn('(COUNT(parent.id) - 1) AS depth');
        }
        $query->addWhere('node.node_left BETWEEN parent.node_left AND parent.node_right');
        $query->addGroupField('node.id');
        if ($p_depth < 1) {
        	$query->addHaving($depthGreater);
        } else {
        	$query->addHaving($depthGreater);
        	$query->addHaving('depth <= ' . $depthMax);
        }
        $query->addOrderBy('node.node_left');
        return $query;
	}

	public static function BuildAllSubtopicsQuery($p_parentId = 0, $p_order = false)
	{
        if (!is_numeric($p_parentId)) {return "";}

        $parent = 0 + $p_parentId;
        $query = "SELECT id FROM Topics WHERE node_left >= (SELECT node_left FROM Topics WHERE id = $parent) AND node_right <= (SELECT node_right FROM Topics WHERE id = $parent)";
        if ($p_order) {
            $query .= " ORDER BY id";
        }

        return $query;
    }

	/**
	 * Returns an SQLSelectClause object that builds a query for retrieving the
	 * subtopics of the given parent.
	 *
	 * @param integer $p_parentId - parent topic identifier
	 * @return SQLSelectClause
	 */
	public static function BuildSubtopicsQueryWithoutDepth($p_parentIds = 0)
	{
        $p_parentIds = is_array($p_parentIds)? $p_parentIds: array($p_parentIds);
        $topicObj = new Topic();
        $query = new SQLSelectClause($p_indent);
        $query->addColumn('node.id AS id');
        $query->setTable($topicObj->m_dbTableName . ' AS node');
        $query->addTableFrom($topicObj->m_dbTableName . ' AS parent');
        $query->addWhere('node.node_left BETWEEN parent.node_left AND parent.node_right');
        foreach($p_parentIds as $p_parentId) {
            $query->addConditionalWhere('parent.id = ' . (int)$p_parentId);
        }
        $query->addOrderBy('node.node_left');
        return $query;
	}


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
		global $g_ado_db;

		$topicObj = new Topic();
		$query = new SQLSelectClause();
		$query->addColumn('node.id');
		$query->addColumn('(COUNT(parent.id) - 1) AS depth');
		$query->setTable($topicObj->m_dbTableName . ' AS node');
		$query->addTableFrom($topicObj->m_dbTableName . ' AS parent');
		$query->addWhere('node.node_left BETWEEN parent.node_left AND parent.node_right');
		if ($p_startingTopicId > 0) {
			$query->addTableFrom($topicObj->m_dbTableName . ' AS sub_parent');
			$query->addWhere('node.node_left > sub_parent.node_left');
			$query->addWhere('node.node_left < sub_parent.node_right');
			$query->addWhere('sub_parent.id = ' . (int)$p_startingTopicId);
		}
		$query->addGroupField('node.id');
		$query->addOrderBy('node.node_left');
		$rows = $g_ado_db->GetAll($query->buildQuery());
        if (empty($rows)) { // empty tree
            return array();
        }

		$p_tree = array();
		$startDepth = null;
		$currentPath = array();
		foreach ($rows as $row) {
			$topicId = $row['id'];
			$depth = $row['depth'] - (int)$startDepth;
			$topic = new Topic($topicId);
			if (is_null($startDepth)) {
				$startDepth = $depth;
				$depth = 0;
				$currentPath[$topicId] = $topic;
			} elseif ($depth > count($currentPath)) {
				$currentPath[$topicId] = $topic;
			} elseif ($depth == 0) {
				$currentPath = array($topicId=>$topic);
			} else {
				while ($depth < count($currentPath)) {
					array_pop($currentPath);
				}
				$currentPath[$topicId] = $topic;
			}
			$p_tree[] = $currentPath;
		}
		return $p_tree;
	} // fn GetTree


    /**
     * Update order for all items in tree.
     *
     * @param array $order
     *      $parent =>  array(
     *          $order => $topicId
     *      );
     *  @return bool
     */
    public static function UpdateOrder(array $p_order)
    {
		global $g_ado_db;

        $orderChanged = false;
        foreach ($p_order as $parentId => $order) {
        	list(, $parentId) = explode('_', $parentId);

        	$parentTopic = new Topic((int)$parentId);
        	$subtopics = $parentTopic->getSubtopics(true);
        	if (count($subtopics) != count($order)) {
        		return false;
        	}

        	foreach ($order as $newTopicOrder => $topicId) {
                list(, $topicId) = explode('_', $topicId);

                if ($subtopics[$newTopicOrder] != $topicId) {
                	$oldTopicOrder = array_search($topicId, $subtopics);
                	self::SwitchTopics($subtopics[$newTopicOrder], $topicId, $parentTopic);

                	$subtopics[$oldTopicOrder] = $subtopics[$newTopicOrder];
                	$subtopics[$newTopicOrder] = $topicId;

                	$orderChanged = true;
                }
            }
        }

        if ($orderChanged) {
        	CampCache::singleton()->clear('user');
        }

        return TRUE;
    } // fn UpdateOrder


    /**
     *
     * @param integer $p_topicId
     * @param integer $p_oldTopicOrder
     * @param integer $p_newTopicOrder
     */
    private static function SwitchTopics($p_leftTopicId, $p_rightTopicId, Topic $p_parentTopic)
    {
    	global $g_ado_db;

    	$topicTable = $p_parentTopic->m_dbTableName;

    	$g_ado_db->Execute("LOCK TABLE `$topicTable`");

		$maxRight = (int)$g_ado_db->GetOne('SELECT MAX(node_right) FROM Topics');

		$leftTopic = new Topic($p_leftTopicId);
		$rightTopic = new Topic($p_rightTopicId);

		// 1. move the left topic to the right by:
		//    max(right) - left_topic.node_left + 1
		// result: [left_of_left] [empty_left] [between_left_&_right] [right]
		//         [right_of_right] .. [end] [left]
		// where empty_left width is left_topic.width + 1
		//       end is the end of the whole topic tree
		$distance = $maxRight - $leftTopic->getLeft() + 1;
		$sql = "UPDATE `$topicTable` "
		. "SET node_left = node_left + $distance, node_right = node_right + $distance "
		. "WHERE node_left >= " . $leftTopic->getLeft()
		. "  AND node_right <= " . $leftTopic->getRight();
		$g_ado_db->Execute($sql);
		$leftTopicTmpLeft = $leftTopic->getLeft() + $distance;
		$leftTopicTmpRight = $leftTopic->getRight() + $distance;

		// 2. move the right topic to the right by:
		//    max(right) - right_topic.node_left + left_topic.width + 2
		// result: [left_of_left] [empty_left] [between_left_&_right] [empty_right]
		//         [right_of_right] .. [end] [left] [right]
		// where empty_right width is right_topic.width + 1
		$distance = $maxRight - $rightTopic->getLeft() + $leftTopic->getWidth() + 2;
		$sql = "UPDATE `$topicTable` "
		. "SET node_left = node_left + $distance, node_right = node_right + $distance "
		. "WHERE node_left >= " . $rightTopic->getLeft()
		. "  AND node_right <= " . $rightTopic->getRight();
		$g_ado_db->Execute($sql);
		$rightTopicTmpLeft = $rightTopic->getLeft() + $distance;
		$rightTopicTmpRight = $rightTopic->getRight() + $distance;

		// 3. move the topics in between the left and right topic to the right by:
		//    right_topic.width - left_topic.width
		// result: [left_of_left] [empty_left] [between_left_&_right] [empty_right]
		//         [right_of_right] .. [end] [left] [right]
		// where empty_left width is right_topic.width + 1
		// where empty_right width is left_topic.width + 1
		$distance = $rightTopic->getWidth() - $leftTopic->getWidth();
		$sql = "UPDATE `$topicTable` "
		. "SET node_left = node_left + $distance, node_right = node_right + $distance "
		. "WHERE node_left > " . $leftTopic->getRight()
		. "  AND node_right < " . $rightTopic->getLeft();
		$g_ado_db->Execute($sql);

		// 4. move the left topic to the left by:
		//    max(right) - right_topic.left + 1 - (right_topic.width - left_topic.width)
		// result: [left_of_left] [empty_left] [between_left_&_right] [left]
		//         [right_of_right] .. [end] [empty] [right]
		// where empty width is left_topic.width + 1
		$distance = $maxRight - $rightTopic->getLeft() + 1 - ($rightTopic->getWidth() - $leftTopic->getWidth());
		$sql = "UPDATE `$topicTable` "
		. "SET node_left = node_left - $distance, node_right = node_right - $distance "
		. "WHERE node_left >= " . $leftTopicTmpLeft
		. "  AND node_right <= " . $leftTopicTmpRight;
		$g_ado_db->Execute($sql);

		// 5. move the right topic to the left by:
		//    max(right) - left_topic.left + 1 + (left_topic.width + 1)
		// result: [left_of_left] [right] [between_left_&_right] [left]
		//         [right_of_right] .. [end]
		// where empty width is left_topic.width + 1
		$distance = $maxRight - $leftTopic->getLeft() + 1 + ($leftTopic->getWidth() + 1);
		$sql = "UPDATE `$topicTable` "
		. "SET node_left = node_left - $distance, node_right = node_right - $distance "
		. "WHERE node_left >= " . $rightTopicTmpLeft
		. "  AND node_right <= " . $rightTopicTmpRight;
		$g_ado_db->Execute($sql);

		$g_ado_db->Execute("UNLOCK TABLE");
    } // fn MoveTopic

} // class Topics

?>
