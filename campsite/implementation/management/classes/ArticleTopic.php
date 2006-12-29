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
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/Topic.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */
class ArticleTopic extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle','TopicId');
	var $m_dbTableName = 'ArticleTopics';
	var $m_columnNames = array('NrArticle', 'TopicId');

	function ArticleTopic()
	{
		parent::DatabaseObject($this->m_columnNames);
	} // constructor

	/**
	 * @return int
	 */
	function getTopicId()
	{
		return $this->m_data['TopicId'];
	} // fn getTopicId


	/**
	 * @return int
	 */
	function getArticleNumber()
	{
		return $this->m_data['NrArticle'];
	} // fn getArticleNumber


	/**
	 * Link a topic to an article.
	 * @param int $p_topicId
	 * @param int $p_articleNumber
	 * @return void
	 */
	function AddTopicToArticle($p_topicId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
					.' VALUES('.$p_articleNumber.', '.$p_topicId.')';
		$g_ado_db->Execute($queryStr);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Topic $1 added to article', $p_topicId);
		Log::Message($logtext, null, 144);
	} // fn AddTopicToArticle


	/**
	 * Unlink a topic from an article.
	 * @param int $p_topicId
	 * @param int $p_articleNumber
	 * @return void
	 */
	function RemoveTopicFromArticle($p_topicId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = "DELETE FROM ArticleTopics WHERE NrArticle=$p_articleNumber AND TopicId=$p_topicId";
		$g_ado_db->Execute($queryStr);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Article topic $1 deleted', $p_topicId);
		Log::Message($logtext, null, 145);
	} // fn RemoveTopicFromArticle


	/**
	 * Remove topic pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	function OnArticleDelete($p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleTopics'
					." WHERE NrArticle='".$p_articleNumber."'";
		$g_ado_db->Execute($queryStr);
	} // fn OnArticleDelete


	/**
	 * Copy the topic pointers
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
	{
		global $g_ado_db;
		$queryStr = 'SELECT * FROM ArticleTopics WHERE NrArticle='.$p_srcArticleNumber;
		$rows = $g_ado_db->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
						." VALUES($p_destArticleNumber, ".$row['TopicId'].")";
			$g_ado_db->Execute($queryStr);
		}
	} // fn OnArticleCopy


	/**
	 * Get the topics for the given article.
	 *
	 * @param int $p_articleNumber
	 *		Retrieve the topics for this article.
	 * @param boolean $p_countOnly
	 * 		Only get the number of topics attached to the article.
	 *
	 * @return mixed
	 * 		Return an array or an int.
	 */
	function GetArticleTopics($p_articleNumber, $p_countOnly = false)
	{
		global $g_ado_db;
		$selectStr = "*";
		if ($p_countOnly) {
			$selectStr = "COUNT(*)";
		}
    	$queryStr = "SELECT $selectStr FROM ArticleTopics "
    				." WHERE NrArticle = $p_articleNumber"
					.' ORDER BY TopicId';
		if ($p_countOnly) {
			return $g_ado_db->GetOne($queryStr);
		} else {
			$rows = $g_ado_db->GetAll($queryStr);
			$topics = array();
			foreach ($rows as $row) {
				$topics[] =& new Topic($row['TopicId']);
			}
			return $topics;
		}
	} // fn GetArticleTopics


	/**
	 * Get the Articles that have the given Topic.
	 * @param int $p_topicId
	 * @return array
	 */
	function GetArticlesWithTopic($p_topicId)
	{
		global $g_ado_db;

		$articleIds = array();
		$queryStr = "SELECT NrArticle FROM ArticleTopics WHERE Topicid = $p_topicId";
		$rows = $g_ado_db->GetAll($queryStr);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$articleIds[] = $row['NrArticle'];
			}
		}

		$queryStr = 'SELECT DISTINCT(ArticleType) FROM TopicFields';
		$rows = $g_ado_db->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = "SELECT FieldName FROM TopicFields WHERE ArticleType = '"
						. $row['ArticleType'] . "'";
			$rows2 = $g_ado_db->GetAll($queryStr);
			if (!is_array($rows2) || sizeof($rows2) == 0) {
				continue;
			}
			$columns = '';
			foreach ($rows2 as $row2) {
				$columns .= " OR F" . $row2['FieldName'] . " = $p_topicId";
			}
			$columns = substr($columns, 3);
			$queryStr = "SELECT DISTINCT(NrArticle) FROM X" . $row['ArticleType']
						. " WHERE $columns";
			$rows2 = $g_ado_db->GetAll($queryStr);
			if (!is_array($rows2)) {
				continue;
			}
			foreach ($rows2 as $row2) {
				foreach ($row2 as $fieldName=>$value) {
					$articleIds[] = $value;
				}
			}
		}

		if (sizeof($articleIds) == 0) {
			return null;
		}

		$articleIds = array_unique($articleIds);
		$tmpArticle =& new Article();
		$columnNames = implode(',', $tmpArticle->getColumnNames(true));
		$queryStr = "SELECT $columnNames FROM Articles WHERE Number IN ("
					. implode(', ', $articleIds) . ")";
    	return DbObjectArray::Create('Article', $queryStr);
	} // fn GetArticlesWithTopic


} // class ArticleTopic

?>