<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Nette\Image as NetteImage;

require_once __DIR__ . '/../../Nette/exceptions.php';

/**
 * Rendition
 */
class Rendition
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var string
     */
    private $specs;

    /**
     * @var string
     */
    private $name;

    /**
     * @param int $width
     * @param int $height
     * @param string $specs
     * @param string $name
     */
    public function __construct($width, $height, $specs = 'fit', $name = null)
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->specs = (string) $specs;
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get width
     *
     * @return void
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get specification
     *
     * @return string
     */
    public function getSpecs()
    {
        return $this->specs;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get preview
     *
     * @param int $width
     * @param int $height
     * @return Newscoop\Image\Rendition
     */
    public function getPreview($width, $height)
    {
        if ($this->width <= $width && $this->height <= $height) { // original smaller
            $width = $this->width;
            $height = $this->height;
        } else if ($this->height <= $height) { // original width bigger
            $height = round((float) $height * (float) $width / (float) $this->width);
        } else if ($this->width <= $width) { // original height bigger
            $width = round((float) $width * (float) $height / (float) $this->height);
        } else {
            $ratio = min((float) $width / (float) $this->width, (float) $height / (float) $this->height);
            $width = round((float) $ratio * (float) $this->width);
            $height = round((float) $ratio * (float) $this->height);
        }

        return new Rendition($width, $height, $this->specs);
    }

    /**
     * Get thumbnail
     *
     * @param string $image
     * @param Newscoop\Image\ImageService $imageService
     * @return Newscoop\Image\Thumbnail
     */
    public function getThumbnail($image, ImageService $imageService)
    {
        $info = getimagesize(APPLICATION_PATH . '/../' . $image);
        list($width, $height) = NetteImage::calculateSize($info[0], $info[1], $this->width, $this->height, $this->getFlags($this->specs));
        return new Thumbnail($imageService->getSrc($image, $this->width, $this->height, $this->specs), min($width, $this->width), min($height, $this->height));
    }

    /**
     * Generate image
     *
     * @param string $imagePath
     * @return Nette\Image
     */
    public function generateImage($imagePath)
    {
        $image = NetteImage::fromFile(APPLICATION_PATH . '/../' . $imagePath);
        $image->resize($this->width, $this->height, $this->getFlags($this->specs));
        if ($this->getFlags($this->specs) === NetteImage::FILL) {
            $image->crop('50%', '50%', $this->width, $this->height);
        }

        return $image;
    }

    /**
     * Get flags
     *
     * @param string $specs
     * @return int
     */
    private function getFlags($specs)
    {
        switch ($specs) {
            case 'fill':
                $flags = NetteImage::FILL;
                break;

            case 'fit':
            default:
                $flags = NetteImage::FIT;
                break;
        }

        return $flags;
    }
}
