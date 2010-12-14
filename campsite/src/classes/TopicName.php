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

} // class TopicName

?>