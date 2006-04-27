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
     * @param array $p_sqlOptions
     * @return array
     */
    function GetUnapprovedComments($p_sqlOptions = null)
    {
        global $PHORUM;
        global $g_ado_db;
        $queryStr = "SELECT * FROM ".$PHORUM['message_table']
                    ." LEFT JOIN Articles "
                    ." ON " . $PHORUM['message_table'] . ".message_id=Articles.comment_thread_id"
                    ." WHERE status<0";
        //echo $queryStr;
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        foreach ($rows as $row) {
            $comment =& new Phorum_message();
            $comment->fetch($row);
            $article =& new Article();
            $article->fetch($row);
            $returnArray[] = array("comment" => $comment, "article" => $article);
        }
        return $returnArray;
    } // fn GetUnapprovedComments


//    function CreateIfNotExist($p_forumId, $p_threadId)
//    {
//        $forum =& new Phorum_forum($p_forumId);
//        if (!$forum->exists()) {
//            $forum->create();
//        }
//        $comment =& new Phorum_message($p_threadId);
//        if (
//    }

} // class ArticleComment
?>