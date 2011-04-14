<?php

/**
 * Render actions view helper
 */
use Newscoop\Entity\Comment\Commenter;

class Admin_View_Helper_CommentCommenter extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentCommenter(Commenter $commenter)
    {
        $this->view->commenter = $commenter;
        return $this->view->render('comment-commenter.phtml');
    }
}
