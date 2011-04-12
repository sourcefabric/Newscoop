<?php
use Newscoop\Entity\CommentsUser;

/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentsUser extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentsUser($user)
    {
        $this->view->user = $user;
        return $this->view->render('comments-user.phtml');
    }
}
