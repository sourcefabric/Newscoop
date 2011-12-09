<?php

/**
 * Render NewsItem
 */
class Admin_View_Helper_RenderNewsItem extends Zend_View_Helper_Abstract
{
    /**
     * Render NewsItem
     *
     * @param Newscoop\News\NewsItem $item
     * @return void
     */
    public function renderNewsItem(\Newscoop\News\NewsItem $item)
    {
        $this->view->item = $item;
        echo $this->view->render('news-item.phtml');
    }
}
