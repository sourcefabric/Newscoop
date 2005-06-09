<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

class IssuePublish extends DatabaseObject {
	var $m_keyColumnNames = array('IdPublication', 'NrIssue', 'IdLanguage', 'PublishTime');
	var $m_dbTableName = 'IssuePublish';
	var $m_columnNames = array('IdPublication', 'NrIssue', 'IdLanguage', 
							   'PublishTime', 'Action', 'PublishArticles');
	
	/**
	 * This table delays an issue's publish time to a later date.
	 *
	 * @param int p_articleId
	 * @param int p_languageId
	 * @param string p_publishTime
	 *
	 */
	function IssuePublish($p_publicationId = null, $p_issueId = null, 
	                      $p_languageId = null, $p_publishTime = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['NrIssue'] = $p_issueId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['PublishTime'] = $p_publishTime;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	
	
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
	 * Set the published state to switch to when the "publish time" arrives.
	 * This can be 'P' for Publish, or 'U' for Unpublish.	 
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
	 * Set the front page state to switch to when the "publish time" arrives.
	 * This can be 'Y' for Yes, or 'N' for No.
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
	function getPublishTime() 
	{
		return $this->m_data['PublishTime'];
	} // fn getPublishTime
	
	
	/**
	 * Get all the events that will change the issue's state.
	 * Returns an array of IssuePublish objects.
	 *
	 * @param int p_publicationId
	 * @param int p_issueId
	 * @param int p_language
	 * @return array
	 */
	function GetIssueEvents($p_publicationId, $p_issueId, $p_languageId = null) 
	{
		global $Campsite;
		$queryStr = "SELECT * FROM IssuePublish "
					." WHERE IdPublication = $p_publicationId "
					." AND NrIssue = $p_issueId "
					." AND IdLanguage = $p_languageId "
					." ORDER BY PublishTime ASC";
		$result =& DbObjectArray::Create('IssuePublish', $queryStr);
		return $result;
	} // fn GetIssueEvents
	
} // class IssuePublish

?>