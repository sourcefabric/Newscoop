<?php

/**
 * Rendition preview view helper
 */
class Admin_View_Helper_RenditionPreview extends Zend_View_Helper_Abstract
{
    /**
     * Get rendition preview
     *
     * @param Newscoop\Image\Rendition $rendition
     * @param int $width
     * @param int $height
     * @param Newscoop\Image\ImageInterface $image
     * @return string
     */
    public function renditionPreview(\Newscoop\Image\Rendition $rendition, $width, $height, \Newscoop\Image\ImageInterface $image)
    {
        $this->view->rendition = $rendition;
        $this->view->preview = $rendition->getPreview($width, $height);
        $this->view->thumbnail = $this->view->preview->getThumbnail($image, Zend_Registry::get('container')->getService('image'));
        $this->view->image = $image;
        return $this->view->render('rendition/preview.phtml');
    }
}
