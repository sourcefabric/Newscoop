<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');

class ArticleComment
{

    /**
     * Get the comment ID for the given article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return int
     */
    function GetCommentThreadId($p_articleNumber, $p_languageId)
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
    function Link($p_articleNumber, $p_languageId, $p_commentId, $p_isFirstMessage = false)
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
    function Unlink($p_articleNumber = null, $p_languageId = null, $p_commentId = null)
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
    function OnArticleDelete($p_articleNumber, $p_languageId)
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
    function GetArticleComments($p_articleNumber, $p_languageId, $p_status = null, $p_countOnly = false)
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
                    ." WHERE ".$PHORUM['message_table'].".thread=$threadId"
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
    function GetComments($p_status = 'approved', $p_getTotal = false, $p_searchString = '', $p_sqlOptions = null)
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
                    ." ON $messageTable". ".message_id=ArticleComments.fk_comment_id)"
                    ." LEFT JOIN Articles ON ArticleComments.fk_article_number=Articles.Number"
                    ." AND ArticleComments.fk_language_id=Articles.IdLanguage";

        $whereQuery = '';
        if ($p_status == 'approved') {
            $whereQuery .= "status > 0";
        } elseif ($p_status == 'unapproved') {
            $whereQuery .= "status < 0";
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
                    $comment =& new Phorum_message();
                    $comment->fetch($row);
                    $article =& new Article();
                    $article->fetch($row);
                    $returnArray[] = array("comment" => $comment, "article" => $article);
                }
            }
            return $returnArray;
        }
    } // fn GetComments


} // class ArticleComment
?>