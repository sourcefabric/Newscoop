<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class TopicName extends DatabaseObject {
	var $m_dbTableName = 'TopicNames';
	var $m_keyColumnNames = array('fk_topic_id', 'fk_language_id');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array('fk_topic_id', 'fk_language_id', 'name');


	/**
	 * Constructor.
	 * @param int $p_id
	 */
	public function __construct($p_idOrName = null, $p_languageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (is_numeric($p_idOrName) && $p_languageId > 0) {
    		$this->m_data['fk_topic_id'] = $p_idOrName;
    		$this->m_data['fk_language_id'] = $p_languageId;
			$this->fetch();
		} elseif (!empty($p_idOrName) && $p_languageId > 0) {
			$this->m_keyColumnNames = array('fk_language_id', 'name');
			$this->m_data['name'] = $p_idOrName;
    		$this->m_data['fk_language_id'] = $p_languageId;
			$this->fetch();
			$this->m_keyColumnNames = array('fk_topic_id', 'fk_language_id');
		}
	} // constructor


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getName();
	}


	/**
	 *
	 * @param int $p_topicId
	 */
	public static function DeleteTopicNames($p_topicId)
	{
		global $g_ado_db;

		$topicNameObj = new TopicName();
		$sql = "DELETE FROM `" . $topicNameObj->m_dbTableName . "` WHERE fk_topic_id = '" . (int)$p_topicId . "'";
		return $g_ado_db->Execute($sql);
	} // fn DeleteTopicNames


	/**
	 *
	 * @param int $p_topicId
	 */
	public static function GetTopicNames($p_topicId)
	{
		global $g_ado_db;

		$topicName = new TopicName();
		$p_topicId = (int)$p_topicId;

		$names = array();
		$sql = 'SELECT * FROM `' . $topicName->m_dbTableName . "` WHERE fk_topic_id = '$p_topicId'";
		$rows = $g_ado_db->GetAll($sql);
		foreach ($rows as $row) {
			$tmpObj = new TopicName();
			$tmpObj->fetch($row);
			$names[$row['fk_language_id']] = $tmpObj;
		}
		return $names;
	} // fn GetTopicNames


	/**
	 * @return int
	 */
	public function getTopicId()
	{
		return $this->m_data['fk_topic_id'];
	} // fn getId


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['name'];
	} // fn getName


	/**
	 *
	 */
	public function setName($p_name)
	{
	    return $this->setProperty('name', $p_name);
	} // fn setName

	public static function BuildTopicIdsQuery($p_topicNames)
	{
        $topics_query = false;
        $topic_names = array();
        $topic_names_full = array();

        foreach ($p_topicNames as $one_name) {
            $one_name = str_replace('"', '""', trim($one_name));
            $one_name_parts = explode(":", $one_name);
            if (2 <= count($one_name_parts)) {
                $topic_name = $one_name_parts[0];
                $topic_lang = $one_name_parts[1];
                $topic_names_full[] = "(name = \"$topic_name\" AND fk_language_id IN (SELECT Id FROM Languages WHERE Code = \"$topic_lang\"))";
            }
            elseif (0 < strlen($one_name)) {
                $topic_names[] = $one_name;
            }
        }

        if ((0 == $topic_names) && ($topic_names_full)) {
            return $topics_query;
        }

        $names_str = 'trim(name) IN ("' . implode('", "', $topic_names) . '") ';
        $names_str_full = '(' . implode(' OR ', $topic_names_full) . ') ';
        $topics_query = "SELECT DISTINCT fk_topic_id AS id FROM TopicNames WHERE ";

        $continuing = "";
        if (0 < count($topic_names)) {
            $topics_query .= $names_str;
            $continuing = "OR ";
        }
        if (0 < count($topic_names_full)) {
            $topics_query .= $continuing;
            $topics_query .= $names_str_full;
        }

        return $topics_query;
    }

} // class BuildTopicIdsQuery

?>
