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
	var $m_keyColumnNames = array('IdPublication', 'NrIssue', 'IdLanguage', 'ActionTime');
	var $m_dbTableName = 'IssuePublish';
	var $m_columnNames = array('IdPublication', 'NrIssue', 'IdLanguage', 
							   'ActionTime', 'Action', 'PublishArticles', 'Completed');
	
	/**
	 * This table delays an issue's publish time to a later date.
	 *
	 * @param int $p_articleId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @param string $p_actionTime
	 *
	 */
	function IssuePublish($p_publicationId = null, $p_issueId = null, 
	                      $p_languageId = null, $p_actionTime = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['NrIssue'] = $p_issueId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['ActionTime'] = $p_actionTime;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor

	
	/**
	 * @return int
	 */
	function getPublicationId()
	{
	    return $this->m_data['IdPublication'];
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getIssueId()
	{
	    return $this->m_data['NrIssue'];
	} // fn getIssueId
	
	
	/**
	 * @return int
	 */
	function getLanguageId() 
	{
	    return $this->m_data['IdLanguage'];
	} // fn getLanguageId
	
	
	/**
	 * Get the published state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'P' for Publish, or 'U' for Unpublish.
	 * @return mixed
	 */ 
	function getPublishAction() 
	{
		return $this->m_data['Action'];
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
			$this->setProperty('Action', $p_value);
		}
	} // fn setPublishAction
	
	
	/**
	 * Get whether to publish the articles when the "publish time" arrives.
	 * This can be 'Y' for 'Yes', or 'N' for 'No'.
	 * @return mixed
	 */
	function getPublishArticlesAction() 
	{
		return $this->m_data['PublishArticles'];
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
			$this->setProperty('PublishArticles', $p_value);
		}
	} // fn setPublishArticlesAction
	
	
	/**
	 * Get the time the event is scheduled to happen.
	 * @return string
	 */
	function getActionTime() 
	{
		return $this->m_data['ActionTime'];
	} // fn getActionTime
	
	
	/**
	 * Mark that this action has been completed.
	 * @return void
	 */
	function setCompleted() 
	{
	    $this->setProperty('Completed', 'Y');
	} // fn setCompleted
	
	
	/**
	 * Execute the action.
	 * @return void
	 */
	function doAction()
	{
		$publicationId = $this->m_data['IdPublication'];
		$issueId = $this->m_data['NrIssue'];
		$languageId = $this->m_data['IdLanguage'];
		$publishAction = $this->m_data['Action'];
		$publishArticlesAction = $this->m_data['PublishArticles'];

		$articleState = ($publishAction == 'P') ? 'Y' : 'S';
		if ($publishArticlesAction == 'Y') {
		    $articles =& Article::GetArticles(null, $issueId, null, $languageId);
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
					." WHERE IdPublication = $p_publicationId "
					." AND NrIssue = $p_issueId "
					." AND IdLanguage = $p_languageId "
					." ORDER BY ActionTime ASC";
		$result =& DbObjectArray::Create('IssuePublish', $queryStr);
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
    	           . " WHERE ActionTime <= '$datetime'"
                   . " AND Completed != 'Y'"
                   . " ORDER BY ActionTime ASC";
        $result =& DbObjectArray::Create('IssuePublish', $queryStr);
        return $result;	
	} // fn GetPendingActions
	
	
	/**
	 * Execute all pending actions.
	 * @return void
	 */
	function DoPendingActions()
	{
        $actions =& IssuePublish::GetPendingActions();
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
        $queryStr = "SELECT ActionTime, Action, PublishArticles, "
                    . implode(",", $columnNames). " FROM Issues, IssuePublish "
                    . " WHERE ActionTime >= '" . $datetime . "'"
                    . " AND Completed != 'Y'"
                    . " AND Issues.IdPublication=IssuePublish.IdPublication"
                    . " AND Issues.Number=IssuePublish.NrIssue"
                    . " AND Issues.IdLanguage=IssuePublish.IdLanguage "
                    . " ORDER BY ActionTime DESC"
                    . " LIMIT $p_limit";
        //echo $queryStr."<br>";
		$rows = $Campsite['db']->GetAll($queryStr);
		$addKeys = array();
		if (count($rows) > 0) {
    		foreach ($rows as $row) {
    		    $row["ObjectType"] = "issue";		    
    		    $addKeys[$row['ActionTime']] = $row;
    		}
		}
        return $addKeys;
	} // fn GetFutureActions
	
} // class IssuePublish

?>