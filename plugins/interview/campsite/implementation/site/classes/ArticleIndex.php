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
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

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

	public function ArticleIndex()
	{
		parent::DatabaseObject($this->m_columnNames);
	} // constructor


	/**
	 * @return int
	 */
	public function getArticleNumber()
	{
		return $this->m_data['NrArticle'];
	} // fn getArticleNumber


	/**
	 * Remove index pointers for the given article.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_sectionId
	 * @param int $p_languageId
	 * @param int $p_articleNumber
	 * @return void
	 */
	public static function OnArticleDelete($p_publicationId, $p_issueId,
	                                       $p_sectionId, $p_languageId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleIndex'
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueId "
					." AND NrSection=$p_sectionId "
					." AND NrArticle=$p_articleNumber "
					." AND IdLanguage=$p_languageId";
		$g_ado_db->Execute($queryStr);
	} // fn OnArticleDelete

} // class ArticleIndex

?>