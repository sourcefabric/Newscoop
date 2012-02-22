<?php

/**
 * Image editor
 */
class Admin_View_Helper_ImageEditor extends Zend_View_Helper_Abstract
{
    /**
     * Get image editor
     *
     * @param Newscoop\Image\Rendition $rendition
     * @param Newscoop\Image\ImageInterface $image
     * @return string
     */
    public function imageEditor(\Newscoop\Image\Rendition $rendition, \Newscoop\Image\ImageInterface $image)
    {
        $this->view->rendition = $rendition;
        $this->view->image = $image;
        return $this->view->render('image/editor.phtml');
    }
}
