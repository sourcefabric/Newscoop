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

/**
 * @package Campsite
 */
class ArticlePublish extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle', 'IdLanguage', 'ActionTime');
	var $m_dbTableName = 'ArticlePublish';
	var $m_columnNames = array('NrArticle', 'IdLanguage', 'ActionTime', 
							   'Publish', 'FrontPage', 'SectionPage', 'Completed');
	
	/**
	 * This table delays an article's publish time to a later date.
	 *
	 * @param int $p_articleId
	 * @param int $p_languageId
	 * @param string $p_actionTime
	 *
	 */
	function ArticlePublish($p_articleId = null, $p_languageId = null, $p_actionTime = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['NrArticle'] = $p_articleId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['ActionTime'] = $p_actionTime;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * Return the article ID associated with this action.
	 * @return int
	 */
	function getArticleId() 
	{
	    return $this->getProperty('NrArticle');
	} // fn getArticleId
	
	
	/**
	 * Return the language ID of the article.
	 * @return int
	 */
	function getLanguageId() 
	{
	    return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	/**
	 * Get the published state to switch to when the "publish time" arrives:
	 * NULL for no action, 'P' for Publish, or 'U' for Unpublish.
	 * @return mixed
	 */ 
	function getPublishAction() 
	{
		return $this->m_data['Publish'];
	} // fn getPublishAction

	
	/**
	 * Set the published state to switch to when the "publish time" arrives:
	 * NULL for no action, 'P' for Publish, or 'U' for Unpublish.	 
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
	 * Execute the action, and mark the action as completed.
	 * @return void
	 */
	function doAction() 
	{
		$publishAction = $this->getPublishAction();
		$frontPageAction = $this->getFrontPageAction();
		$sectionPageAction = $this->getSectionPageAction();
        $article =& new Article(null, null, null, $this->m_data['IdLanguage'], $this->m_data['NrArticle']);
        $article->setKey(array('Number', 'IdLanguage'));
		if ($publishAction == 'P') {
		    $article->setPublished('Y');
		}
		if ($publishAction == 'U') {
            $article->setPublished('S');   
		}
		if ($frontPageAction == 'S') {
		    $article->setOnFrontPage(true);
		}
		if ($frontPageAction == 'R') {
		    $article->setOnFrontPage(false);
		}
		if ($sectionPageAction == 'S') {
		    $article->setOnSectionPage(true);
		}
		if ($sectionPageAction == 'R') {
		    $article->setOnSectionPage(false);
		}
		$this->setCompleted();	    
	} // fn doAction
	
	
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
		$queryStr .= ' ORDER BY ActionTime ASC ';
		$result =& DbObjectArray::Create('ArticlePublish', $queryStr);
		return $result;
	} // fn GetArticleEvents
	
	
	/**
	 * Get all the actions that currently need to be performed.
	 * @return array
	 */
	function GetPendingActions() 
	{
	    global $Campsite;
	    $datetime = strftime("%Y-%m-%d %H:%M:00");
        $queryStr = "SELECT * FROM ArticlePublish "
                    . " WHERE ActionTime <= '$datetime'"
                    . " AND Completed != 'Y'"
                    . " ORDER BY ActionTime ASC";
        $result =& DbObjectArray::Create('ArticlePublish', $queryStr);
        return $result;
	} // fn GetPendingActions
	
	
	/**
	 * Execute all pending actions.
	 * @return void
	 */
	function DoPendingActions() 
	{
        $actions =& ArticlePublish::GetPendingActions();
    	foreach ($actions as $articlePublishObj) {
    	    $articlePublishObj->doAction();
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
	    $dummyArticle =& new Article();
	    $columnNames = $dummyArticle->getColumnNames(true);
        $queryStr = "SELECT ActionTime, Publish, FrontPage, SectionPage,"
                    . implode(",", $columnNames). " FROM Articles, ArticlePublish "
                    . " WHERE ActionTime >= '" . $datetime . "'"
                    . " AND Completed != 'Y'"
                    . " AND Articles.Number=ArticlePublish.NrArticle "
                    . " AND Articles.IdLanguage=ArticlePublish.IdLanguage "
                    . " ORDER BY ActionTime DESC"
                    . " LIMIT $p_limit";
		$rows = $Campsite['db']->GetAll($queryStr);
		$addKeys = array();
		if ($rows && (count($rows) > 0)) {
    		foreach ($rows as $row) {
    		    $row["ObjectType"] = "article";
    		    $addKeys[$row['ActionTime']] = $row;
    		}
		}
        return $addKeys;
	} // fn GetFutureActions
	
} // class ArticlePublish

?>