<?php
use Newscoop\Entity\CommentsUser;

/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentsIndex extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentsIndex($comment)
    {
        $this->view->comment = $comment;
        return $this->view->render('comments-index.phtml');
    }
}
