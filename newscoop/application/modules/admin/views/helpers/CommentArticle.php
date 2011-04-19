<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentArticle extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentArticle($comment)
    {
        $this->view->comment = $comment;
        return $this->view->render('comment-article.phtml');
    }
}
