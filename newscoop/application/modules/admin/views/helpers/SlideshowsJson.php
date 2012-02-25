<?php

/**
 * Slideshows Json
 */
class Admin_View_Helper_SlideshowsJson extends Zend_View_Helper_Abstract
{
    /**
     * Provides slideshows formated for json
     *
     * @param array $slideshows
     * @param int $width
     * @param int $height
     * @return array
     */
    public function SlideshowsJson(array $slideshows, $width = 150, $height = 150)
    {
        return array_map(function($slideshow) use ($width, $height) {
            return (object) array(
                'id' => $slideshow->getId(),
                'headline' => $slideshow->getHeadline(),
                'itemsCount' => $slideshow->getItemsCount(),
                'slug' => $slideshow->getSlug(),
                'items' => array_map(function($item) use ($width, $height) {
                    return (object) array(
                        'caption' => $item->getCaption(),
                        'thumbnail' => $item->getRendition()->getPreview($width, $height)->getThumbnail($item->getImage(), Zend_Registry::get('container')->getService('image')),
                    );
                }, array_filter($slideshow->getItems()->toArray(), function($item) { return $item->isImage(); })),
            );
        }, $slideshows);
    }
}
