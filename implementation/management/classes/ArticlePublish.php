<?php 
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class ArticlePublish extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle', 'IdLanguage', 'PublishTime');
	var $m_dbTableName = 'ArticlePublish';
	var $m_columnNames = array('NrArticle', 'IdLanguage', 'PublishTime', 
							   'Publish', 'FrontPage', 'SectionPage');
	
	/**
	 * This table delays an article's publish time to a later date.
	 *
	 * @param int $p_articleId
	 * @param int $p_languageId
	 * @param string $p_publishTime
	 *
	 */
	function ArticlePublish($p_articleId = null, $p_languageId = null, $p_publishTime = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['NrArticle'] = $p_articleId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['PublishTime'] = $p_publishTime;
		if (!is_null($p_articleId) && !is_null($p_languageId) && !is_null($p_publishTime)) {
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
		return $this->m_data['Publish'];
	} // fn getPublishAction

	
	/**
	 * Set the published state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'P' for Publish, or 'U' for Unpublish.	 
	 * @return void
	 */
	function setPublishAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'P') || ($p_value == 'U') ) {
			$this->setProperty('Publish', $p_value);
		}
		elseif (is_null($p_value)) {
			$this->setProperty('Publish', 'NULL', true, true);
		}
	} // fn setPublishAction
	
	
	/**
	 * Get the front page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function getFrontPageAction() 
	{
		return $this->m_data['FrontPage'];
	} // fn getFrontPageAction
	
	
	/**
	 * Set the front page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function setFrontPageAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'S') || ($p_value == 'R') ) {
			$this->setProperty('FrontPage', $p_value);
		}
		elseif (is_null($p_value)) {
			$this->setProperty('FrontPage', 'NULL', true, true);
		}
	} // fn setFrontPageAction
	
	
	/**
	 * Get the section page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function getSectionPageAction() 
	{
		return $this->m_data['SectionPage'];
	} // fn getSectionPageAction
	
	
	/**
	 * Set the section page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function setSectionPageAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'S') || ($p_value == 'R') ) {
			$this->setProperty('SectionPage', $p_value);
		}
		elseif (is_null($p_value)) {
			$this->setProperty('SectionPage', 'NULL', true, true);
		}
	} // fn setSectionPageAction

	
	/**
	 * Get the time the event is scheduled to happen.
	 * @return string
	 */
	function getPublishTime() 
	{
		return $this->m_data['PublishTime'];
	} // fn getPublishTime
	
	
	/**
	 * Get all the events that will change the article's state.
	 * Returns an array of ArticlePublish objects.
	 *
	 * @param int $p_articleId
	 * @param int $p_languageId
	 * @return array
	 */
	function GetArticleEvents($p_articleId, $p_languageId = null) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM ArticlePublish '
					." WHERE NrArticle=$p_articleId";
		if (!is_null($p_languageId)) {
			$queryStr .= " AND IdLanguage=$p_languageId ";
		}
		$queryStr .= ' ORDER BY PublishTime ASC ';
		$result =& DbObjectArray::Create('ArticlePublish', $queryStr);
		return $result;
	} // fn GetArticleEvents
	
} // class ArticlePublish

?>