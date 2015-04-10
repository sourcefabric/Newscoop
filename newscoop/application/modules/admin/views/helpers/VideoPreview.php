<?php

/**
 * Video preview
 */
class Admin_View_Helper_VideoPreview extends Zend_View_Helper_Abstract
{
    /**
     * Get video preview
     *
     * @param  string $url
     * @param  int    $width
     * @param  int    $height
     * @return string
     */
    public function videoPreview($url, $width = 150, $height = 150)
    {
        $this->view->width = $width;
        $this->view->height = $height;
        $this->view->code = array_pop(explode('/', trim(str_replace('watch?v=', '', $url), '/')));
        if (strpos($url, 'vimeo') !== false || is_numeric($url)) {
            return $this->view->render('slideshow/vimeo-preview.phtml');
        } else {
            return $this->view->render('slideshow/youtube-preview.phtml');
        }
    }
}
