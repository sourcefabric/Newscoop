<?php

/**
 * Slideshow item preview
 */
class Admin_View_Helper_SlideshowItemPreview extends Zend_View_Helper_Abstract
{
    /**
     * Get slideshow item preview
     *
     * @param Newscoop\Package\Item $item
     * @return string
     */
    public function slideshowItemPreview(\Newscoop\Package\Item $item)
    {
        $this->view->itemHelper = $item;
        return $this->view->render('slideshow/item-preview.phtml');
    }
}
