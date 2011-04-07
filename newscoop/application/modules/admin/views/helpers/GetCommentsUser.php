<?php
use Newscoop\Entity\CommentsUser;

/**
 * Render actions view helper
 */
class Admin_View_Helper_GetCommentsUser extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function getCommentsUser($commentsUser)
    {
        $this->view->commentsUser = $commentsUser;
        return $this->view->render('comments-user.phtml');
    }
}
