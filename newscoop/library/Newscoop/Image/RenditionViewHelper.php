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
    protected $imageService;

    /**
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get rendition preview
     *
     * @param Rendition $rendition
     * @param int $width
     * @param int $height
     * @param Newscoop\Image\ArticleRendition $articleRendition
     * @return string
     */
    public function rendition(Rendition $rendition, $width, $height, ArticleRendition $articleRendition = null)
    {
        if ($articleRendition !== null) {
            $preview = $articleRendition->getRendition()->getPreview($width, $height);
            $preview->setCoords($articleRendition->getImageSpecs());
            $thumbnail = $rendition->fits($articleRendition->getImage()) ? $this->imageService->getThumbnail($preview, $articleRendition->getImage()) : null;
        } else {
            $preview = $rendition->getPreview($width, $height);
            $thumbnail = null;
        }

        $this->view->isDefault = $thumbnail === null || $articleRendition->isDefault();
        $this->view->preview = $preview;
        $this->view->rendition = $rendition;
        $this->view->thumbnail = $thumbnail;
        return $this->view->render('image/rendition.phtml');
    }
}
