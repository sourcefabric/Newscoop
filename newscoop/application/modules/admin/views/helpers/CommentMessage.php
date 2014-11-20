<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentMessage extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @return void
     */
    public function commentMessage($comment)
    {
        $this->view->comment = $comment;
        return $this->view->render('comment-message.phtml');
    }
}
