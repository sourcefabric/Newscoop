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
class ArticleIndex extends DatabaseObject {
	var $m_keyColumnNames = array(
		'IdPublication',
		'IdLanguage',
		'IdKeyword',
		'NrIssue',
		'NrSection',
		'NrArticle');
	var $m_dbTableName = 'ArticleIndex';
	var $m_columnNames = array(
		'IdPublication',
		'IdLanguage',
		'IdKeyword',
		'NrIssue',
		'NrSection',
		'NrArticle');
	
	function ArticleIndex() 
	{ 
		parent::DatabaseObject($this->m_columnNames);
	} // constructor
	
	
	/**
	 * @return int
	 */
	function getArticleNumber() 
	{
		return $this->getProperty('NrArticle');
	} // fn getArticleNumber

	
	/**
	 * Remove index pointers for the given article.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_sectionId
	 * @param int $p_languageId
	 * @param int $p_articleId
	 * @return void
	 */
	function OnArticleDelete($p_publicationId, $p_issueId, $p_sectionId, $p_languageId, $p_articleId) 
	{
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleIndex'
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueId "
					." AND NrSection=$p_sectionId "
					." AND NrArticle=$p_articleId "
					." AND IdLanguage=$p_languageId";
		$Campsite['db']->Execute($queryStr);		
	} // fn OnArticleDelete
	
} // class ArticleIndex

?>