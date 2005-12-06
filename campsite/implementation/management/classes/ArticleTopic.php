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
		global $Campsite;
		$logtext = getGS('Topic $1 added to article', $p_topicId);
		$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
					.' VALUES('.$p_articleNumber.', '.$p_topicId.')';
		$Campsite['db']->Execute($queryStr);
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
		global $Campsite;
		$logtext = getGS('Article topic $1 deleted', $p_topicId); 
		$queryStr = "DELETE FROM ArticleTopics WHERE NrArticle=$p_articleNumber AND TopicId=$p_topicId";
		$Campsite['db']->Execute($queryStr);
		Log::Message($logtext, null, 145);		
	} // fn RemoveTopicFromArticle
	
	
	/**
	 * Remove topic pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	function OnArticleDelete($p_articleNumber) 
	{
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleTopics'
					." WHERE NrArticle='".$p_articleNumber."'";
		$Campsite['db']->Execute($queryStr);		
	} // fn OnArticleDelete
	
	
	/**
	 * Copy the topic pointers
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM ArticleTopics WHERE NrArticle='.$p_srcArticleNumber;
		$rows = $Campsite['db']->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
						." VALUES($p_destArticleNumber, ".$row['TopicId'].")";
			$Campsite['db']->Execute($queryStr);
		}
	} // fn OnArticleCopy

	
	/**
	 * Get the topics for the given article.
	 *
	 * @param int $p_articleNumber
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
	function GetArticleTopics($p_articleNumber, $p_languageId = null, $p_sqlOptions = null) 
	{
		$tmpTopic =& new Topic();
		$columnNames = implode(',', $tmpTopic->getColumnNames(true));
    	$queryStr = "SELECT $columnNames FROM ArticleTopics, Topics "
    				." WHERE ArticleTopics.NrArticle = $p_articleNumber"
    				.' AND ArticleTopics.TopicId = Topics.Id ';
    	if (!is_null($p_languageId)) {
    		$queryStr .= " AND Topics.LanguageId=$p_languageId";
    	}
		$queryStr .= ' ORDER BY Topics.Name ';
    	$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
    	return DbObjectArray::Create('Topic', $queryStr);
	} // fn GetArticleTopics
	
	
	/**
	 * Get the Articles that have the given Topic.
	 * @param int $p_topicId
	 * @return array
	 */
	function GetArticlesWithTopic($p_topicId, $p_languageId = null, $p_sqlOptions = null)
	{
		$tmpArticle =& new Article();
		$columnNames = implode(',', $tmpArticle->getColumnNames(true));
		
		$queryStr = "SELECT $columnNames FROM ArticleTopics, Articles"
					." WHERE ArticleTopics.TopicId=$p_topicId"
					." AND ArticleTopics.NrArticle=Articles.Number";
    	if (!is_null($p_languageId)) {
    		$queryStr .= " AND Topics.LanguageId=$p_languageId";
    	}
    	$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
    	return DbObjectArray::Create('Article', $queryStr);		
	} // fn GetArticlesWithTopic
	
	
} // class ArticleTopic

?>