<?php

/**
 * Slideshow item view helper
 */
class Admin_View_Helper_SlideshowItem extends Zend_View_Helper_Abstract
{
    /**
     * Get slideshow item
     *
     * @param Newscoop\Package\Item $item
     * @return string
     */
    public function slideshowItem(\Newscoop\Package\Item $item, $current = null)
    {
        $this->view->item = $item;
        $this->view->current = $current;
        return $this->view->render('slideshow/item.phtml');
    }
}
