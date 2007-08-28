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
require_once($g_documentRoot.'/classes/SQLSelectClause.php');
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
		return $this->m_data['fk_attachment_id'];
	} // fn getAttachmentId


	/**
	 * @return int
	 */
	function getArticleNumber()
	{
		return $this->m_data['fk_article_number'];
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
		global $g_ado_db;
		$queryStr = 'INSERT IGNORE INTO ArticleAttachments(fk_article_number, fk_attachment_id)'
					.' VALUES('.$p_articleNumber.', '.$p_attachmentId.')';
		$g_ado_db->Execute($queryStr);
	} // fn AddFileToArticle


	/**
	 * Get all the attachments that belong to this article.
	 * @param int $p_articleNumber
	 * @param int $p_languageId
	 * @return array
	 */
	function GetAttachmentsByArticleNumber($p_articleNumber, $p_languageId = null)
	{
		global $g_ado_db;
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
					.' ORDER BY Attachments.time_created asc, Attachments.file_name asc';
		$rows = $g_ado_db->GetAll($queryStr);
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
		global $g_ado_db;
		$queryStr = "DELETE FROM ArticleAttachments WHERE fk_attachment_id=$p_attachmentId";
		$g_ado_db->Execute($queryStr);
	} // fn OnAttachmentDelete


	/**
	 * Remove attachment pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	function OnArticleDelete($p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleAttachments'
					." WHERE fk_article_number='".$p_articleNumber."'";
		$g_ado_db->Execute($queryStr);
	} // fn OnArticleDelete


	/**
	 * Copy all the pointers for the given article.
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
	{
		global $g_ado_db;
		$queryStr = 'SELECT fk_attachment_id FROM ArticleAttachments WHERE fk_article_number='.$p_srcArticleNumber;
		$rows = $g_ado_db->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleAttachments(fk_article_number, fk_attachment_id)'
						." VALUES($p_destArticleNumber, ".$row['fk_attachment_id'].")";
			$g_ado_db->Execute($queryStr);
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
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleAttachments'
					.' WHERE fk_article_number='.$p_articleNumber
					.' AND fk_attachment_id='.$p_attachmentId
					.' LIMIT 1';
		$g_ado_db->Execute($queryStr);
	} // fn RemoveAttachmentFromArticle


    /**
     * Gets an article attachments list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     *
     * @return array $articleAttachmentsList
     *    An array of Attachment objects
     */
    public static function GetList($p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0)
    {
        global $g_ado_db;

        if (!is_array($p_parameters)) {
            return null;
        }

        $sqlClauseObj = new SQLSelectClause();

        $tmpAttachment =& new Attachment();
		$columnNames = $tmpAttachment->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $sqlClauseObj->addColumn($columnName);
        }

        // sets the base table Attachment
        $sqlClauseObj->setTable($tmpAttachment->getDbTableName());
        unset($tmpAttachment);

        // adds the ArticleAttachments join and condition to the query
        $sqlClauseObj->addTableFrom('ArticleAttachments');
        $sqlClauseObj->addWhere('ArticleAttachments.fk_attachment_id = Attachments.id');

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (sizeof($comparisonOperation) < 1) {
                break;
            }

            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
            $sqlClauseObj->addWhere($whereCondition);
        }

        if (!is_array($p_order)) {
            $p_order = array();
        }

        foreach ($p_order as $orderColumn => $orderDirection) {
            $sqlClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        $sqlClauseObj->setLimit($p_start, $p_limit);

        $sqlQuery = $sqlClauseObj->buildQuery();
        $attachments = $g_ado_db->GetAll($sqlQuery);
        if (!is_array($attachments)) {
            return null;
        }

        $articleAttachmentsList = array();
        foreach ($attachments as $attachment) {
            $attchObj = new Attachment($attachment['id']);
            if ($attchObj->exists()) {
                $articleAttachmentsList[] =& $attchObj;
            }
        }

        return $articleAttachmentsList;
    } // fn GetList


    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $comparisonOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'article_nr':
            $comparisonOperation['left'] = 'ArticleAttachments.fk_article_number';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        case 'language':
            if (strtolower($p_param->getRightOperand()) == 'current') {
                $comparisonOperation['left'] = 'Attachments.fk_language_id';
                $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            }
            break;
        }

        if (isset($comparisonOperation['left'])) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $comparisonOperation;
    } // fn ProcessListParameters

} // class ArticleAttachment

?>