<?php

require_once($_SERVER['DOCUMENT_ROOT']."/include/phorum_load.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Phorum_message.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');

class ArticleComment
{

    function GetCommentThreadId($p_articleNumber, $p_languageId)
    {
        global $g_ado_db;
        $queryStr = "SELECT fk_comment_thread_id FROM ArticleComments"
                    ." WHERE fk_article_number=$p_articleNumber"
                    ." AND fk_language_id=$p_languageId";
        $threadId = $g_ado_db->GetOne($queryStr);
        return $threadId;
    } // fn GetCommentThreadId


    function Link($p_articleNumber, $p_languageId, $p_commentId)
    {
        global $g_ado_db;
        $queryStr = "INSERT INTO ArticleComments "
                    ." SET fk_article_number=$p_articleNumber,"
                    ." fk_language_id=$p_languageId,"
                    ." fk_comment_thread_id=$p_commentId";
        $g_ado_db->Execute($queryStr);
    } // fn


    /**
     * Get all comments associated with the given article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return array
     */
    function GetArticleComments($p_articleNumber, $p_languageId)
    {
        global $PHORUM;
        global $g_ado_db;
        $threadId = ArticleComment::GetCommentThreadId($p_articleNumber, $p_languageId);
        $queryStr ="SELECT * FROM ".$PHORUM['message_table']
                    ." WHERE ".$PHORUM['message_table'].".thread=$threadId"
                    ." ORDER BY message_id";
        $messages = DbObjectArray::Create("Phorum_message", $queryStr);
        return $messages;
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
                    ." ON $messageTable". ".message_id=ArticleComments.fk_comment_thread_id)"
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

        $baseQuery .= " ORDER BY ".$PHORUM['message_table'].".message_id";
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