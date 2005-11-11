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
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/Issue.php');

/**
 * @package Campsite
 */
class IssuePublish extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_dbTableName = 'IssuePublish';
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('id',
							   'fk_publication_id', 
							   'fk_issue_id', 
							   'fk_language_id', 
							   'time_action', 
							   'publish_action', 
							   'do_publish_articles', 
							   'is_completed');
	
	/**
	 * This table delays an issue's publish time to a later date.
	 *
	 * @param int $p_id
	 */
	function IssuePublish($p_id = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['id'] = $p_id;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor

	
	/**
	 * @return int
	 */
	function getPublicationId()
	{
	    return $this->m_data['fk_publication_id'];
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getIssueNumber()
	{
	    return $this->m_data['fk_issue_id'];
	} // fn getIssueNumber
	
	
	/**
	 * @return int
	 */
	function getLanguageId() 
	{
	    return $this->m_data['fk_language_id'];
	} // fn getLanguageId
	
	
	/**
	 * Get the published state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'P' for Publish, or 'U' for Unpublish.
	 * @return mixed
	 */ 
	function getPublishAction() 
	{
		return $this->m_data['publish_action'];
	} // fn getPublishAction

	
	/**
	 * Set the published state to switch to when the "publish time" arrives:
	 * 'P' for Publish, or 'U' for Unpublish.	 
	 * @param string $p_value
	 * @return void
	 */
	function setPublishAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'P') || ($p_value == 'U') ) {
			$this->setProperty('publish_action', $p_value);
		}
	} // fn setPublishAction
	
	
	/**
	 * Get whether to publish the articles when the "publish time" arrives.
	 * This can be 'Y' for 'Yes', or 'N' for 'No'.
	 * @return mixed
	 */
	function getPublishArticlesAction() 
	{
		return $this->m_data['do_publish_articles'];
	} // fn getPublishArticlesAction
	
	
	/**
	 * Set the front page state to switch to when the "publish time" arrives:
	 * 'Y' for Yes, or 'N' for No.
	 * @param string $p_value
	 * @return mixed
	 */
	function setPublishArticlesAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'Y') || ($p_value == 'N') ) {
			$this->setProperty('do_publish_articles', $p_value);
		}
	} // fn setPublishArticlesAction
	
	
	/**
	 * Get the time the event is scheduled to happen.
	 * @return string
	 */
	function getActionTime() 
	{
		return $this->m_data['time_action'];
	} // fn getActionTime
	
	
	/**
	 * Mark that this action has been completed.
	 * @return void
	 */
	function setCompleted() 
	{
	    $this->setProperty('is_completed', 'Y');
	} // fn setCompleted
	
	
	/**
	 * Execute the action.
	 * @return void
	 */
	function doAction()
	{
		$publicationId = $this->m_data['fk_publication_id'];
		$issueId = $this->m_data['fk_issue_id'];
		$languageId = $this->m_data['fk_language_id'];
		$publishAction = $this->m_data['publish_action'];
		$publishArticlesAction = $this->m_data['do_publish_articles'];

		$articleState = ($publishAction == 'P') ? 'Y' : 'S';
		if ($publishArticlesAction == 'Y') {
		    $articles = Article::GetArticles(null, $issueId, null, $languageId);
            foreach ($articles as $article) {
                $article->setPublished($articleState);
            }
		}
		$issueState = ($publishAction == 'P') ? 'Y' : 'N';
		$issue =& new Issue($publicationId, $languageId, $issueId);
		$issue->setPublished($issueState);
		$this->setCompleted();
	} // fn doAction
	

	/**
	 * Get all the events that will change the issue's state.
	 * Returns an array of IssuePublish objects.
	 *
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_language
	 * @return array
	 */
	function GetIssueEvents($p_publicationId, $p_issueId, $p_languageId = null) 
	{
		global $Campsite;
		$queryStr = "SELECT * FROM IssuePublish "
					." WHERE fk_publication_id = $p_publicationId "
					." AND fk_issue_id = $p_issueId "
					." AND fk_language_id = $p_languageId "
					." ORDER BY time_action ASC";
		$result = DbObjectArray::Create('IssuePublish', $queryStr);
		return $result;
	} // fn GetIssueEvents
	
	
	/**
	 * Get all the actions that currently need to be performed.
	 * @return array
	 */
	function GetPendingActions() 
	{
	    $datetime = strftime("%Y-%m-%d %H:%M:00");
    	$queryStr = "SELECT * FROM IssuePublish "
    	           . " WHERE time_action <= '$datetime'"
                   . " AND is_completed != 'Y'"
                   . " ORDER BY time_action ASC";
        $result = DbObjectArray::Create('IssuePublish', $queryStr);
        return $result;	
	} // fn GetPendingActions
	
	
	/**
	 * Execute all pending actions.
	 * @return void
	 */
	function DoPendingActions()
	{
        $actions = IssuePublish::GetPendingActions();
    	foreach ($actions as $issuePublishObj) {
    	    $issuePublishObj->doAction();
    	}	    
	} // fn DoPendingActions

	
	/**
	 * For now, this is mostly a hack to get the home page working.
	 * The raw array is returned.
	 *
	 * @param int $p_limit
	 * @return array
	 */
	function GetFutureActions($p_limit) 
	{
	    global $Campsite;
	    $datetime = strftime("%Y-%m-%d %H:%M:00");
	    $dummyIssue =& new Issue();
	    $columnNames = $dummyIssue->getColumnNames(true);
        $queryStr = "SELECT id, time_action, publish_action, do_publish_articles, "
                    . implode(",", $columnNames). " FROM Issues, IssuePublish "
                    . " WHERE time_action >= '" . $datetime . "'"
                    . " AND is_completed != 'Y'"
                    . " AND Issues.IdPublication=IssuePublish.fk_publication_id"
                    . " AND Issues.Number=IssuePublish.fk_issue_id"
                    . " AND Issues.IdLanguage=IssuePublish.fk_language_id "
                    . " ORDER BY time_action DESC"
                    . " LIMIT $p_limit";
        //echo $queryStr."<br>";
		$rows = $Campsite['db']->GetAll($queryStr);
		$addKeys = array();
		if ($rows && (count($rows) > 0)) {
    		foreach ($rows as $row) {
    		    $row["ObjectType"] = "issue";		    
    		    $addKeys[$row['time_action']] = $row;
    		}
		}
        return $addKeys;
	} // fn GetFutureActions
	
} // class IssuePublish

?>