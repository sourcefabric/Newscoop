<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentIndex extends Zend_View_Helper_Abstract
{
    private $index = 1;
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function commentIndex($comment)
    {
        $this->view->comment = $comment;
        $this->view->index = $this->index++;
        return $this->view->render('comment-index.phtml');
    }
}
