<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

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
	public function ArticleAttachment($p_articleNumber = null, $p_attachmentId = null)
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
	public function getAttachmentId()
	{
		return $this->m_data['fk_attachment_id'];
	} // fn getAttachmentId


	/**
	 * @return int
	 */
	public function getArticleNumber()
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
	public static function AddFileToArticle($p_attachmentId, $p_articleNumber)
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
	public static function GetAttachmentsByArticleNumber($p_articleNumber, $p_languageId = null)
	{
		global $g_ado_db;
		$tmpObj = new Attachment();
		$columnNames = implode(',', $tmpObj->getColumnNames());

		if (is_null($p_languageId)) {
			$langConstraint = "FALSE";
		} else {
			$langConstraint = "Attachments.fk_language_id = " . $g_ado_db->escape($p_languageId);
		}
		$queryStr = 'SELECT '.$columnNames
					.' FROM Attachments, ArticleAttachments'
					.' WHERE ArticleAttachments.fk_article_number = ' . $g_ado_db->escape($p_articleNumber)
					.' AND ArticleAttachments.fk_attachment_id=Attachments.id'
					." AND (Attachments.fk_language_id IS NULL OR $langConstraint)"
					.' ORDER BY Attachments.time_created asc, Attachments.file_name asc';
		$rows = $g_ado_db->GetAll($queryStr);
		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpAttachment = new Attachment();
				$tmpAttachment->fetch($row);
				$returnArray[] = $tmpAttachment;
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
	public static function OnAttachmentDelete($p_attachmentId)
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
	public static function OnArticleDelete($p_articleNumber)
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
	public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
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
	public static function RemoveAttachmentFromArticle($p_attachmentId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleAttachments'
					.' WHERE fk_article_number='.$p_articleNumber
					.' AND fk_attachment_id='.$p_attachmentId
					.' LIMIT 1';
		$g_ado_db->Execute($queryStr);
	} // fn RemoveAttachmentFromArticle

    /**
     * Returns an article attachments list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     * @param integer $p_count
     *    The total count of the elements; this count is computed without
     *    applying the start ($p_start) and limit parameters ($p_limit)
     *
     * @return array $articleAttachmentsList
     *    An array of Attachment objects
     */
    public static function GetList(array $p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$paramsArray['parameters'] = serialize($p_parameters);
        	$paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray['start'] = $p_start;
        	$paramsArray['limit'] = $p_limit;
        	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
        	$articleAttachmentsList = $cacheListObj->fetchFromCache();
        	if ($articleAttachmentsList !== false
        	&& is_array($articleAttachmentsList)) {
        		return $articleAttachmentsList;
        	}
        }

        $hasArticleNr = false;
        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessParameters($param);
            if (sizeof($comparisonOperation) < 1) {
                break;
            }

            if (strpos($comparisonOperation['left'], 'fk_article_number')) {
                $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
                $hasArticleNr = true;
            } elseif (strpos($comparisonOperation['left'], 'fk_language_id')) {
                $whereCondition = '('.$comparisonOperation['left'].' IS NULL OR '
                    .$comparisonOperation['left']." = "
                    .$g_ado_db->escape($comparisonOperation['right']).")";
            } else {
                $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
            }
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // validates whether article number was given
        if ($hasArticleNr === false) {
            CampTemplate::singleton()->trigger_error('missed parameter Article '
                .'Number in statement list_article_attachments');
            return;
        }

        // sets the columns to be fetched
        $tmpAttachment = new Attachment();
		$columnNames = $tmpAttachment->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }
        $countClauseObj->addColumn('COUNT(*)');

        // sets the main table for the query
        $selectClauseObj->setTable($tmpAttachment->getDbTableName());
        $countClauseObj->setTable($tmpAttachment->getDbTableName());
        unset($tmpAttachment);

        // adds the ArticleAttachments join and condition to the query
        $selectClauseObj->addTableFrom('ArticleAttachments');
        $selectClauseObj->addWhere('ArticleAttachments.fk_attachment_id = Attachments.id');
        $countClauseObj->addTableFrom('ArticleAttachments');
        $countClauseObj->addWhere('ArticleAttachments.fk_attachment_id = Attachments.id');

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the order condition if any
        foreach ($p_order as $orderColumn => $orderDirection) {
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $attachments = $g_ado_db->GetAll($selectQuery);
        if (is_array($attachments)) {
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of attachment objects
        	$articleAttachmentsList = array();
        	foreach ($attachments as $attachment) {
        		$attchObj = new Attachment($attachment['id']);
        		if ($attchObj->exists()) {
        			$articleAttachmentsList[] = $attchObj;
        		}
        	}
        } else {
        	$articleAttachmentsList = array();
        	$p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($articleAttachmentsList);
        }

        return $articleAttachmentsList;
    } // fn GetList


    /**
     * Processes a parameter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation
     *      The array containing processed values of the condition
     */
    private static function ProcessParameters($p_param)
    {
        $comparisonOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'article_number':
            $comparisonOperation['left'] = 'ArticleAttachments.fk_article_number';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        case 'language_id':
            $comparisonOperation['left'] = 'Attachments.fk_language_id';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        case 'status':
            $comparisonOperation['right'] = strtolower($p_param->getRightOperand());
            if ($comparisonOperation['right'] == 'approved'
            || $comparisonOperation['right'] == 'unapproved') {
                $comparisonOperation['left'] = 'Attachments.Status';
            }
            break;
        }

        if (isset($comparisonOperation['left'])) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $comparisonOperation;
    } // fn ProcessParameters

} // class ArticleAttachment

?>
