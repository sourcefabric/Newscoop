<?php

/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentCommenter extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentUser($user)
    {
        $this->view->user = $user;
        return $this->view->render('comment-user.phtml');
    }
}
