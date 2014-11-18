<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Get thumbnail
 */
class ThumbnailViewHelper extends \Zend_View_Helper_Abstract
{
    /**
     * @var Newscoop\Image\ImageService
     */
    protected $imageService;

    /**
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get thumbnail for given image
     *
     * @param string $image
     * @param int $width
     * @param int $height
     * @param string $specs
     * @return mixin
     */
    public function thumbnail($image, $width, $height, $specs)
    {
        if (is_string($image)) {
            $image = new LocalImage($image);
        }

        return $this->imageService->getThumbnail(new Rendition($width, $height, $specs), $image);
    }
}
