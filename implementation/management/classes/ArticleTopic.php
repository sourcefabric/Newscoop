<?
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');

class ArticleTopic extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle','TopicId');
	var $m_dbTableName = 'ArticleTopics';
	var $m_columnNames = array('NrArticle', 'TopicId');
	
	function ArticleTopic() { }
	
	/**
	 * @return int
	 */
	function getTopicId() {
		return $this->getProperty('TopicId');
	} // fn getTopicId
	
	
	/**
	 * @return int
	 */
	function getArticleId() {
		return $this->getProperty('NrArticle');
	} // fn getArticleId

	
	/**
	 * Remove topic pointers for the given article.
	 * @param int p_articleId
	 * @return void
	 */
	function OnArticleDelete($p_articleId) {
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleTopics'
					." WHERE NrArticle='".$p_articleId."'";
		$Campsite['db']->Execute($queryStr);		
	} // fn OnArticleDelete
	
	
	/**
	 * Copy the topic pointers
	 * @param int p_srcArticleId
	 * @param int p_destArticleId
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleId, $p_destArticleId) {
		global $Campsite;
		$queryStr = 'SELECT * FROM ArticleTopics WHERE NrArticle='.$p_srcArticleId;
		$rows = $Campsite['db']->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
						." VALUES($p_destArticleId, ".$row['TopicId'].")";
			$Campsite['db']->Execute($queryStr);
		}
	} // fn OnArticleCopy
	
} // class ArticleTopic

?>