<?php

/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList\Meta;

/**
 * Slideshow item exposition for templates
 */
class SlideshowItemMeta extends MetaBase
{
    public function getImage()
    {
        return new \MetaImage($this->dataObject->getImage()->getId());
    }

    public function getIsImage()
    {
        return $this->dataObject->isImage();
    }

    public function getSlideshow()
    {
        return new \Newscoop\TemplateList\Meta\SlideshowsMeta($this->dataObject->getPackage());
    }

    public function getVideo()
    {
        return (object) array(
            'url' => $this->dataObject->getVideoUrl(),
            'width' => $this->dataObject->getRendition()->getWidth(),
            'height' => $this->dataObject->getRendition()->getHeight(),
        );
    }
}
