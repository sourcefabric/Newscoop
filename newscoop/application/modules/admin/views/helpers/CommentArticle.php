<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_CommentArticle extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @return void
     */
    public function commentArticle($article)
    {
        $this->view->article = $article;
        return $this->view->render('comment-article.phtml');
    }
}
