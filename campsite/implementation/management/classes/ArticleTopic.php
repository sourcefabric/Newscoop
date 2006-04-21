<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
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
		return $this->getProperty('TopicId');
	} // fn getTopicId


	/**
	 * @return int
	 */
	function getArticleNumber()
	{
		return $this->getProperty('NrArticle');
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
		if (function_exists("camp_load_language")) { camp_load_language("api");	}
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
		if (function_exists("camp_load_language")) { camp_load_language("api");	}
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
	 *
	 * @return array
	 */
	function GetArticleTopics($p_articleNumber)
	{
		global $g_ado_db;
    	$queryStr = "SELECT DISTINCT(Topics.Id) FROM ArticleTopics, Topics "
    				." WHERE ArticleTopics.NrArticle = $p_articleNumber"
    				.' AND ArticleTopics.TopicId = Topics.Id '
					.' ORDER BY Topics.Id ';
		$topicIds = array();
		$rows = $g_ado_db->GetAll($queryStr);
		if ($rows && is_array($rows)) {
			foreach ($rows as $row) {
				$topicIds[] = $row['Id'];
			}
		}

		// read topics from article type fields
/*		$queryStr = "SELECT Type FROM Articles WHERE Number = $p_articleNumber";
		$articleType = $g_ado_db->GetOne($queryStr);
		$queryStr = "SELECT FieldName FROM TopicFields WHERE ArticleType = '$articleType'";
		$rows2 = $g_ado_db->GetAll($queryStr);
		if (is_array($rows2) && sizeof($rows2) > 0) {
			$columns = '';
			foreach ($rows2 as $row2) {
				$columns .= ", F" . $row2['FieldName'];
			}
			$columns = substr($columns, 2);
			$queryStr = "SELECT $columns FROM X$articleType WHERE NrArticle = $p_articleNumber";
			$rows2 = $g_ado_db->GetAll($queryStr);
			if (is_array($rows2)) {
				foreach ($rows2 as $row2) {
					foreach ($row2 as $fieldName=>$value) {
						$topicIds[] = $value;
					}
				}
			}
		}
		$topicIds = array_unique($topicIds);
*/
		$topics = array();
		foreach ($topicIds as $topicId) {
			$topics[] =& new Topic($topicId);
		}
		return $topics;
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