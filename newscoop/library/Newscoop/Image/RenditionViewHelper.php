<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Get rendition
 */
class RenditionViewHelper extends \Zend_View_Helper_Abstract
{
    /**
     * @var Newscoop\Image\ImageService
     */
    private $imageService;

    /**
     * @param Newscoop\Image\ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get rendition preview
     *
     * @param Newscoop\Image\Rendition $rendition
     * @param int $width
     * @param int $height
     * @param Newscoop\Image\ArticleRendition $articleImageRendition
     * @return string
     */
    public function rendition(Rendition $rendition, $width, $height, ArticleRendition $articleRendition = null)
    {
        $preview = $articleRendition !== null ? $articleRendition->getRendition()->getPreview($width, $height) : $rendition->getPreview($width, $height);
        $thumbnail = $articleRendition !== null ? $this->imageService->getThumbnail($preview, $articleRendition->getImage()->getPath()) : null;
        return sprintf('<div class="preview" style="width: %dpx; height: %dpx;">%s</div>', $preview->getWidth(), $preview->getHeight(), $thumbnail !== null ? $thumbnail->getImg($this->view) : '');
    }
}
