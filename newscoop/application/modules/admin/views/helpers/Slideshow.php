<?php

/**
 * Slideshow
 */
class Admin_View_Helper_Slideshow extends Zend_View_Helper_Abstract
{
    /**
     * Get slideshow
     *
     * @param Newscoop\Package\Package $slideshow
     * @return string
     */
    public function slideshow(\Newscoop\Package\Package $slideshow)
    {
        $this->view->helperSlideshow = $slideshow;
        return $this->view->render('slideshow/slideshow.phtml');
    }
}
