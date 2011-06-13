<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentAction extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentAction($comment)
    {
        $this->view->comment = $comment;
        return $this->view->render('comment-action.phtml');
    }
}
