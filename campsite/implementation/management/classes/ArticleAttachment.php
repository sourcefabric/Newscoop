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
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Attachment.php');
//require_once($g_documentRoot.'/classes/Article.php');

/**
 * @package Campsite
 */
class ArticleAttachment extends DatabaseObject {
	var $m_keyColumnNames = array('fk_article_number', 'fk_attachment_id');
	var $m_dbTableName = 'ArticleAttachments';
	var $m_columnNames = array('fk_article_number', 'fk_attachment_id');
	
	/**
	 * The article attachment table links together articles with Attachments.
	 *
	 * @param int $p_articleNumber
	 * @param int $p_attachmentId
	 * @return ArticleAttachment
	 */
	function ArticleAttachment($p_articleNumber = null, $p_attachmentId = null) 
	{ 
		if (is_numeric($p_articleNumber)) {
			$this->m_data['fk_article_number'] = $p_articleNumber;
		}
		if (is_numeric($p_attachmentId)) {
			$this->m_data['fk_attachment_id'] = $p_attachmentId;
		}
	} // constructor
	

	/**
	 * @return int
	 */
	function getAttachmentId() 
	{
		return $this->getProperty('fk_attachment_id');
	} // fn getAttachmentId
	
	
	/**
	 * @return int
	 */
	function getArticleNumber() 
	{
		return $this->getProperty('fk_article_number');
	} // fn getArticleNumber


	/**
	 * Link the given file with the given article.  
	 *
	 * @param int $p_attachmentId
	 * @param int $p_articleNumber
	 *
	 * @return void
	 */
	function AddFileToArticle($p_attachmentId, $p_articleNumber) 
	{
		global $Campsite;
		$queryStr = 'INSERT IGNORE INTO ArticleAttachments(fk_article_number, fk_attachment_id)'
					.' VALUES('.$p_articleNumber.', '.$p_attachmentId.')';
		$Campsite['db']->Execute($queryStr);
	} // fn AddFileToArticle

	
	/**
	 * Get all the attachments that belong to this article.
	 * @param int $p_articleNumber
	 * @param int $p_languageId
	 * @return array
	 */
	function GetAttachmentsByArticleNumber($p_articleNumber, $p_languageId = null) 
	{
		global $Campsite;
		$tmpObj =& new Attachment();
		$columnNames = implode(',', $tmpObj->getColumnNames());
		
		if (is_null($p_languageId)) {
			$langConstraint = "FALSE";
		} else {
			$langConstraint = "Attachments.fk_language_id=$p_languageId";
		}
		$queryStr = 'SELECT '.$columnNames
					.' FROM Attachments, ArticleAttachments'
					.' WHERE ArticleAttachments.fk_article_number='.$p_articleNumber
					.' AND ArticleAttachments.fk_attachment_id=Attachments.id'
					." AND (Attachments.fk_language_id IS NULL OR $langConstraint)"
					.' ORDER BY Attachments.time_created';
		$rows = $Campsite['db']->GetAll($queryStr);
		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpAttachment =& new Attachment();
				$tmpAttachment->fetch($row);
				$returnArray[] =& $tmpAttachment;
			}
		}
		return $returnArray;
	} // fn GetAttachmentsByArticleNumber
	

	/**
	 * This is called when an attachment is deleted.
	 * It will disassociate the file from all articles.
	 *
	 * @param int $p_attachmentId
	 * @return void
	 */
	function OnAttachmentDelete($p_attachmentId) 
	{
		global $Campsite;
		$queryStr = "DELETE FROM ArticleAttachments WHERE fk_attachment_id=$p_attachmentId";
		$Campsite['db']->Execute($queryStr);
	} // fn OnAttachmentDelete
	
		
	/**
	 * Remove attachment pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	function OnArticleDelete($p_articleNumber) 
	{
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleAttachments'
					." WHERE fk_article_number='".$p_articleId."'";
		$Campsite['db']->Execute($queryStr);		
	} // fn OnArticleDelete
	

	/**
	 * Copy all the pointers for the given article.
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber) 
	{
		global $Campsite;
		$queryStr = 'SELECT fk_attachment_id FROM ArticleAttachments WHERE fk_article_id='.$p_srcArticleNumber;
		$rows = $Campsite['db']->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleAttachments(fk_article_number, fk_attachment_id)'
						." VALUES($p_destArticleNumber, ".$row['fk_attachment_id'].")";
			$Campsite['db']->Execute($queryStr);
		}
	} // fn OnArticleCopy
	

	/**
	 * Remove the linkage between the given attachment and the given article.
	 *
	 * @param int $p_attachmentId
	 * @param int $p_articleNumber
	 *
	 * @return void
	 */
	function RemoveAttachmentFromArticle($p_attachmentId, $p_articleNumber) 
	{
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleAttachments'
					.' WHERE fk_article_number='.$p_articleNumber
					.' AND fk_attachment_id='.$p_attachmentId
					.' LIMIT 1';
		$Campsite['db']->Execute($queryStr);
	} // fn RemoveAttachmentFromArticle

} // class ArticleAttachment

?>