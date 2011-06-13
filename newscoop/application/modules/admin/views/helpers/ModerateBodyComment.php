<?php

/**
 * Render actions view helper
 */
use Newscoop\Entity\Comment;

class Admin_View_Helper_ModerateBodyComment extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function moderateBodyComment(Comment $comment)
    {
        $this->view->comment = $comment;
        return $this->view->render('moderate-body-comment.phtml');
    }
}
