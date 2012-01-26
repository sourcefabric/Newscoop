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
    public function slideshowItem(\Newscoop\Package\Item $item)
    {
        $this->view->item = $item;
        return $this->view->render('slideshow/item.phtml');
    }
}
