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
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/include/campsite_init.php');

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
	public function IssuePublish($p_id = null)
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
	public function getEventId()
	{
		return $this->m_data['id'];
	} // fn getEventId


	/**
	 * @return int
	 */
	public function getPublicationId()
	{
	    return $this->m_data['fk_publication_id'];
	} // fn getPublicationId


	/**
	 * Set the publication ID.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	public function setPublicationId($p_value)
	{
		return $this->setProperty('fk_publication_id', $p_value);
	} // fn setPublicationId


	/**
	 * @return int
	 */
	public function getIssueNumber()
	{
	    return $this->m_data['fk_issue_id'];
	} // fn getIssueNumber


	/**
	 * Enter description here...
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	public function setIssueNumber($p_value)
	{
		return $this->setProperty('fk_issue_id', $p_value);
	} // fn setIssueNumber


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
	    return $this->m_data['fk_language_id'];
	} // fn getLanguageId


	/**
	 * Set the language ID of the issue to publish.
	 *
	 * @param int $p_value
	 * @return boolean
	 */
	public function setLanguageId($p_value)
	{
		return $this->setProperty('fk_language_id', $p_value);
	} // fn setLanguageId


	/**
	 * Get the published state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'P' for Publish, or 'U' for Unpublish.
	 * @return mixed
	 */
	public function getPublishAction()
	{
		return $this->m_data['publish_action'];
	} // fn getPublishAction


	/**
	 * Set the published state to switch to when the "publish time" arrives:
	 * 'P' for Publish, or 'U' for Unpublish.
	 * @param string $p_value
	 * @return void
	 */
	public function setPublishAction($p_value)
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
	public function getPublishArticlesAction()
	{
		return $this->m_data['do_publish_articles'];
	} // fn getPublishArticlesAction


	/**
	 * Set the front page state to switch to when the "publish time" arrives:
	 * 'Y' for Yes, or 'N' for No.
	 * @param string $p_value
	 * @return mixed
	 */
	public function setPublishArticlesAction($p_value)
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'Y') || ($p_value == 'N') ) {
			$this->setProperty('do_publish_articles', $p_value);
		}
	} // fn setPublishArticlesAction


	/**
	 * Get the time the event is scheduled to happen.
	 * Will be returned in the format: YYYY-MM-DD HH:MM:SS
	 *
	 * @return string
	 */
	public function getActionTime()
	{
		return $this->m_data['time_action'];
	} // fn getActionTime


	/**
	 * Set the time the action should be executed.
	 *
	 * @param string $p_value
	 * 		Must be in the form YYYY-MM-DD HH:MM:SS
	 *
	 * @return boolean
	 */
	public function setActionTime($p_value)
	{
		return $this->setProperty('time_action', $p_value);
	} // fn setActionTime


	/**
	 * Return true if the action has been carried out.
	 *
	 * @return boolean
	 */
	public function isCompleted()
	{
		return ($this->m_data['is_completed'] == 'Y');
	} // fn isCompleted


	/**
	 * Mark that this action has been completed.
	 * @return void
	 */
	public function setCompleted()
	{
	    $this->setProperty('is_completed', 'Y');
	} // fn setCompleted


	/**
	 * Execute the action.
	 * @return void
	 */
	public function doAction()
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
                $article->setWorkflowStatus($articleState);
            }
		}
		$issueState = ($publishAction == 'P') ? 'Y' : 'N';
		$issue = new Issue($publicationId, $languageId, $issueId);
		$issue->setWorkflowStatus($issueState);
		$this->setCompleted();
	} // fn doAction


	/**
	 * Get all the events that will change the issue's state.
	 * Returns an array of IssuePublish objects.
	 *
	 * @param int $p_publicationId
	 * @param int $p_issueNumber
	 * @param int $p_languageId
	 * @param boolean $p_includeCompleted
	 * @return array
	 */
	public static function GetIssueEvents($p_publicationId, $p_issueNumber,
	                                      $p_languageId = null, $p_includeCompleted = true)
	{
		$queryStr = "SELECT * FROM IssuePublish "
					." WHERE fk_publication_id = $p_publicationId "
					." AND fk_issue_id = $p_issueNumber "
					." AND fk_language_id = $p_languageId ";
		if (!$p_includeCompleted) {
			$queryStr .= " AND is_completed = 'N'";
		}
		$queryStr .= " ORDER BY time_action ASC";
		$result = DbObjectArray::Create('IssuePublish', $queryStr);
		return $result;
	} // fn GetIssueEvents


	/**
	 * Get all the actions that currently need to be performed.
	 * @return array
	 */
	public static function GetPendingActions()
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
	 * @return integer
	 */
	public static function DoPendingActions()
	{
        $actions = IssuePublish::GetPendingActions();
    	foreach ($actions as $issuePublishObj) {
    	    $issuePublishObj->doAction();
    	}
    	if (count($actions) > 0) {
    		CampCache::singleton()->clear('user');
    	}
        return count($actions);
	} // fn DoPendingActions


	/**
	 * For now, this is mostly a hack to get the home page working.
	 * The raw array is returned.
	 *
	 * @param int $p_limit
	 * @return array
	 */
	public static function GetFutureActions($p_limit)
	{
	    global $g_ado_db;
	    $datetime = strftime("%Y-%m-%d %H:%M:00");
	    $dummyIssue = new Issue();
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
		$rows = $g_ado_db->GetAll($queryStr);
		$addKeys = array();
		if ($rows && (count($rows) > 0)) {
    		foreach ($rows as $row) {
    		    $row["ObjectType"] = "issue";
    		    $addKeys[$row['time_action']] = $row;
    		}
		}
        return $addKeys;
	} // fn GetFutureActions


	/**
	 * This should be called whenever an issue is deleted.
	 *
	 * @param int $p_issueNumber
	 */
	public static function OnIssueDelete($p_publicationId, $p_issueNumber, $p_languageId)
	{
		global $g_ado_db;
		$queryStr = "DELETE FROM IssuePublish "
					." WHERE fk_publication_id = $p_publicationId "
					." AND fk_issue_id = $p_issueNumber "
					." AND fk_language_id = $p_languageId ";
		return $g_ado_db->Execute($queryStr);
	} // fn OnIssueDelete

} // class IssuePublish

?>