<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/include/phorum_load.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_message.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');

class ArticleComment
{
	/**
	 * Returns an Article object to which the comment identified
	 * by the given id belongs to. Returns null if invalid message id.
	 * @param $p_messageId
	 * @return Article
	 */
	public static function GetArticleOf($p_messageId)
	{
		global $g_ado_db;
		$p_messageId = (int)$p_messageId;
		$sql = "SELECT * FROM ArticleComments WHERE fk_comment_id = $p_messageId";
		$res = $g_ado_db->GetAll($sql);
		if (is_array($res) && count($res) > 0) {
			$articleNo = $res[0]['fk_article_number'];
			$languageId = $res[0]['fk_language_id'];
			$article = new Article($languageId, $articleNo);
			return $article;
		}
		return null;
	}


    /**
     * Get the comment ID for the given article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return int
     */
    public function GetCommentThreadId($p_articleNumber, $p_languageId)
    {
        global $g_ado_db;
        $queryStr = "SELECT fk_comment_id FROM ArticleComments"
                    ." WHERE fk_article_number=$p_articleNumber"
                    ." AND fk_language_id=$p_languageId"
                    ." AND is_first=1";
        $threadId = $g_ado_db->GetOne($queryStr);
        return $threadId;
    } // fn GetCommentThreadId


    /**
     * Link the given article to the given comment.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param int $p_commentId
     * @return void
     */
    public static function Link($p_articleNumber, $p_languageId,
                                $p_commentId, $p_isFirstMessage = false)
    {
        global $g_ado_db;
        $p_isFirstMessage = $p_isFirstMessage ? '1' : '0';
        $queryStr = "INSERT INTO ArticleComments "
                    ." SET fk_article_number=$p_articleNumber,"
                    ." fk_language_id=$p_languageId,"
                    ." fk_comment_id=$p_commentId,"
                    ." is_first=$p_isFirstMessage";
        $g_ado_db->Execute($queryStr);
    } // fn Link


    /**
     * Remove all the entries from the table that match the given parameters.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param int $p_commentId
     */
    public static function Unlink($p_articleNumber = null, $p_languageId = null,
                                  $p_commentId = null)
    {
        global $g_ado_db;
        $constraints = array();
        if (!is_null($p_articleNumber)) {
        	$constraints[] = "fk_article_number=$p_articleNumber";
        }
        if (!is_null($p_languageId)) {
        	$constraints[] = "fk_language_id=$p_languageId";
        }
        if (!is_null($p_commentId)) {
        	$constraints[] = "fk_comment_id=$p_commentId";
        }
        $queryStr = "DELETE FROM ArticleComments WHERE "
        			.implode(" AND ", $constraints);
        $g_ado_db->Execute($queryStr);
    } // fn Unlink


    /**
     * This function should be called whenever an article is deleted.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return void
     */
    public static function OnArticleDelete($p_articleNumber, $p_languageId)
    {
    	if (!is_numeric($p_articleNumber) || !is_numeric($p_languageId)) {
    		return;
    	}

    	$threadId = ArticleComment::GetCommentThreadId($p_articleNumber, $p_languageId);
		// Delete all comments for this article
		$threadHead = new Phorum_message($threadId);
		$threadHead->delete(PHORUM_DELETE_TREE);

		// Delete all links to this article.
		ArticleComment::Unlink($p_articleNumber, $p_languageId);
    } // fn OnArticleDelete


    /**
     * Get all comments associated with the given article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param string $p_status
     *      This can be NULL if you dont care about the status,
     *      "approved" or "unapproved".
     * @param boolean $p_countOnly
     * @return array
     */
    public static function GetArticleComments($p_articleNumber, $p_languageId,
                                              $p_status = null, $p_countOnly = false)
    {
        global $PHORUM;
        global $g_ado_db;
        $threadId = ArticleComment::GetCommentThreadId($p_articleNumber,
                                                       $p_languageId);
        if (!$threadId) {
        	if ($p_countOnly) {
        		return 0;
        	} else {
            	return null;
        	}
        }

        // Are we counting or getting the comments?
        $selectClause = "*";
        if ($p_countOnly) {
            $selectClause = "COUNT(*)";
        }

        // Only getting comments with a specific status?
        $whereClause = "";
        if (!is_null($p_status)) {
            if ($p_status == "approved") {
                $whereClause = " AND status=".PHORUM_STATUS_APPROVED;
            } elseif ($p_status == "unapproved") {
                $whereClause = " AND status=".PHORUM_STATUS_HIDDEN;
            }
        }
        $queryStr = "SELECT $selectClause "
                    ." FROM ".$PHORUM['message_table']
                    ." WHERE thread=$threadId"
                    ." AND message_id != thread"
                    . $whereClause
                    ." ORDER BY message_id";
        if ($p_countOnly) {
        	$count = $g_ado_db->GetOne($queryStr);
       		return $count;
        } else {
	        $messages = DbObjectArray::Create("Phorum_message", $queryStr);
	        return $messages;
        }
    } // fn GetArticleComments


    /**
     * Get the comments and their associated articles.
     *
     * @param string $p_status
     *      Can be 'approved' or 'unapproved'.
     * @param boolean $p_getTotal
     *      If TRUE, return the number of comments that match the search
     *      criteria and not the actual records.
     * @param string $p_searchString
     *      A string to search for.
     * @param array $p_sqlOptions
     *      See DatabaseObject::ProcessOptions().
     * @return array
     */
    public static function GetComments($p_status = 'approved', $p_getTotal = false,
                                       $p_searchString = '', $p_sqlOptions = null)
    {
        global $PHORUM;
        global $g_ado_db;

        $messageTable = $PHORUM['message_table'];

        $selectClause = "*";
        if ($p_getTotal) {
            $selectClause = "COUNT(*)";
        }

        $baseQuery = "SELECT $selectClause FROM ($messageTable"
                    ." LEFT JOIN ArticleComments "
                    ." ON $messageTable". ".thread=ArticleComments.fk_comment_id)"
                    ." LEFT JOIN Articles ON ArticleComments.fk_article_number=Articles.Number"
                    ." AND ArticleComments.fk_language_id=Articles.IdLanguage";

        $whereQuery = "$messageTable.message_id != $messageTable.thread";
        if ($p_status == 'approved') {
            $whereQuery .= " AND status > 0";
        } elseif ($p_status == 'unapproved') {
            $whereQuery .= " AND status < 0";
        }

        if (!empty($p_searchString)) {
            $p_searchString = mysql_real_escape_string($p_searchString);
            if (!empty($whereQuery)) {
                $whereQuery .= " AND ";
            }
            $whereQuery .="($messageTable.subject LIKE '%$p_searchString%'"
                        ." OR $messageTable.body LIKE '%$p_searchString%'"
                        ." OR $messageTable.email LIKE '%$p_searchString%'"
                        ." OR $messageTable.author LIKE '%$p_searchString%'"
                        ." OR $messageTable.ip LIKE '%$p_searchString%')";
        }

        if (!empty($whereQuery)) {
            $baseQuery .= " WHERE ".$whereQuery;
        }

        // Default ORDER BY clause
        if (is_null($p_sqlOptions) || !isset($p_sqlOptions['ORDER BY'])) {
           $baseQuery .= " ORDER BY ".$PHORUM['message_table'].".message_id";
        }

        //echo $baseQuery."<br><br>";
        if ($p_getTotal) {
            $numComments = $g_ado_db->GetOne($baseQuery);
            return $numComments;
        } else {
            $queryStr = DatabaseObject::ProcessOptions($baseQuery, $p_sqlOptions);
            //echo $queryStr;
            $rows = $g_ado_db->GetAll($queryStr);
            $returnArray = array();
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $comment = new Phorum_message();
                    $comment->fetch($row);
                    $article = new Article();
                    $article->fetch($row);
                    $returnArray[] = array("comment" => $comment, "article" => $article);
                }
            }
            return $returnArray;
        }
    } // fn GetComments


    /**
     * Returns an article comments list based on the given parameters.
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
     * @return array $articleCommentsList
     *    An array of Comment objects
     */
    public static function GetList(array $p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db, $PHORUM;

        if (CampCache::IsEnabled()) {
        	$paramsArray['parameters'] = serialize($p_parameters);
        	$paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray['start'] = $p_start;
        	$paramsArray['limit'] = $p_limit;
        	$cacheListObj = new CampCacheList($paramsArray, __CLASS__);
        	$articleCommentsList = $cacheListObj->fetchFromCache();
        	if ($articleCommentsList !== false && is_array($articleCommentsList)) {
        		return $articleCommentsList;
        	}
        }

        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        $messageTable = $PHORUM['message_table'];
        $selectClauseObj->setTable($messageTable);
        $countClauseObj->setTable($messageTable);

        $articleNumber = null;
        $languageId = null;
        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);

            if (strtolower($comparisonOperation->getLeftOperand()) == 'fk_article_number') {
                $articleNumber = $comparisonOperation->getRightOperand();
            }
            if (strtolower($comparisonOperation->getLeftOperand()) == 'fk_language_id') {
                $languageId = $comparisonOperation->getRightOperand();
            }
            $parameters[] = $comparisonOperation;
        }

        if (!is_null($articleNumber) && !is_null($languageId)) {
        	// gets the thread id for the article
        	$threadId = ArticleComment::GetCommentThreadId($articleNumber, $languageId);
            $selectClauseObj->addWhere('thread = '.$threadId);
            $countClauseObj->addWhere('thread = '.$threadId);
        }

        $selectClauseObj->addWhere('message_id != thread');
        $selectClauseObj->addWhere('status = '.PHORUM_STATUS_APPROVED);
        $countClauseObj->addWhere('message_id != thread');
        $countClauseObj->addWhere('status = '.PHORUM_STATUS_APPROVED);

        if (!is_array($p_order) || count($p_order) == 0) {
            $p_order = array('default'=>'asc');
        }

        // sets the order condition if any
        if (is_array($p_order)) {
            $order = ArticleComment::ProcessListOrder($p_order);
            // sets the order condition if any
            foreach ($order as $orderDesc) {
                $orderField = $orderDesc['field'];
                $orderDirection = $orderDesc['dir'];
                $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
            }
        }

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $comments = $g_ado_db->GetAll($selectQuery);
        if (is_array($comments)) {
        	$countClauseObj->addColumn('COUNT(*)');
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of comment objects
        	$articleCommentsList = array();
        	foreach ($comments as $comment) {
        		$pmObj = new Phorum_message($comment['message_id']);
        		if ($pmObj->exists()) {
        			$articleCommentsList[] = $pmObj;
        		}
        	}
        } else {
        	$articleCommentsList = array();
        	$p_count = 0;
        }
        if (CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($articleCommentsList);
        }

        return $articleCommentsList;
    } // fn GetList


    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $parameter
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        switch (strtolower($p_param->getLeftOperand())) {
        case 'article_number':
            return new ComparisonOperation('fk_article_number',
                                           new Operator('is', 'integer'),
                                           (int) $p_param->getRightOperand());
        case 'language_id':
            return new ComparisonOperation('fk_language_id',
                                           new Operator('is', 'integer'),
                                           (int) $p_param->getRightOperand());
        }
    } // fn ProcessListParameters

    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $dbField = null;
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            switch (strtolower($field)) {
                case 'default':
                    $dbField = 'thread_order';
                    break;
                case 'bydate':
                    $dbField = 'datestamp';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
                $order[] = array('field'=>$dbField, 'dir'=>$direction);
            }
        }
        return $order;
    }

} // class ArticleComment
?>
