<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($g_documentRoot.'/classes/DatabaseObject.php');

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
	function getArticleId() 
	{
		return $this->getProperty('NrArticle');
	} // fn getArticleId

	
	/**
	 * Link a topic to an article.
	 * @param int $p_topicId
	 * @param int $p_articleId
	 * @return void
	 */
	function AddTopicToArticle($p_topicId, $p_articleId) 
	{
		global $Campsite;
		$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
					.' VALUES('.$p_articleId.', '.$p_topicId.')';
		$Campsite['db']->Execute($queryStr);
	} // fn AddTopicToArticle
	
	
	/**
	 * Unlink a topic from an article.
	 * @param int $p_topicId
	 * @param int $p_articleId
	 * @return void
	 */
	function RemoveTopicFromArticle($p_topicId, $p_articleId) 
	{
		global $Campsite;
		$queryStr = "DELETE FROM ArticleTopics WHERE NrArticle=$p_articleId AND TopicId=$p_topicId";
		$Campsite['db']->Execute($queryStr);
	} // fn RemoveTopicFromArticle
	
	
	/**
	 * Remove topic pointers for the given article.
	 * @param int $p_articleId
	 * @return void
	 */
	function OnArticleDelete($p_articleId) 
	{
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleTopics'
					." WHERE NrArticle='".$p_articleId."'";
		$Campsite['db']->Execute($queryStr);		
	} // fn OnArticleDelete
	
	
	/**
	 * Copy the topic pointers
	 * @param int $p_srcArticleId
	 * @param int $p_destArticleId
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleId, $p_destArticleId) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM ArticleTopics WHERE NrArticle='.$p_srcArticleId;
		$rows = $Campsite['db']->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
						." VALUES($p_destArticleId, ".$row['TopicId'].")";
			$Campsite['db']->Execute($queryStr);
		}
	} // fn OnArticleCopy

	
	/**
	 * Get the topics for the given article.
	 *
	 * @param int $p_articleId
	 *		Retrieve the topics for this article.
	 *
	 * @param int $p_numTopics
	 *		The max number of topics to return.
	 *
	 * @param int $p_start
	 * 		Start listing the topics from this index.
	 *
	 * @return array
	 */
	function GetArticleTopics($p_articleId, $p_languageId = null, $p_sqlOptions = null) 
	{
		global $Campsite;
		$tmpTopic =& new Topic();
		$columnNames = implode(',', $tmpTopic->getColumnNames(true));
    	$queryStr = "SELECT $columnNames FROM ArticleTopics, Topics "
    				." WHERE ArticleTopics.NrArticle = $p_articleId"
    				.' AND ArticleTopics.TopicId = Topics.Id ';
    	if (!is_null($p_languageId) && is_numeric($p_languageId)) {
    		$queryStr .= " AND Topics.LanguageId=$p_languageId";
    	}
		$queryStr .= ' ORDER BY Topics.Name ';
    	$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
    	return DbObjectArray::Create('Topic', $queryStr);
	} // fn GetArticleTopics
	
} // class ArticleTopic

?>