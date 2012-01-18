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
        list($width, $height) = NetteImage::calculateSize($info[0], $info[1], $this->width, $this->height, $this->getFlags());
        if ($this->isCrop()) {
            $width = min($width, $this->width);
            $height = min($height, $this->height);
        }

        return new Thumbnail($imageService->getSrc($image, $this->width, $this->height, $this->specs), $width, $height);
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
        if ($this->isCrop()) {
            $cropSpecs = explode('_', $this->specs);
            if (count($cropSpecs) === 1) {
                $image->resize($this->width, $this->height, $this->getFlags());
                $image->crop('50%', '50%', $this->width, $this->height);
            } else {
                list(, $x0, $y0, $x1, $y1) = $cropSpecs;
                $image->crop($x0, $y0, $x1 - $x0, $y1 - $y0);
                $image->resize($this->width, $this->height, $this->getFlags());
            }
        } else {
            $image->resize($this->width, $this->height, $this->getFlags());
        }

        return $image;
    }

    /**
     * Generate image
     *
     * @param Newscoop\Image\ImageInterface $image
     * @return Nette\Image
     */
    public function generate(ImageInterface $image)
    {
        return $this->generateImage($image->getPath());
    }

    /**
     * Get aspect ratio
     *
     * @return float
     */
    public function getAspectRatio()
    {
        return (float) $this->width / (float) $this->height;
    }

    /**
     * Get select area
     *
     * @param Newscoop\Image\ImageInterface $image
     * @return array
     */
    public function getSelectArea(ImageInterface $image)
    {
        if ($this->isCrop()) {
            $cropSpecs = explode('_', $this->specs);
            if (count($cropSpecs) > 1) {
                array_shift($cropSpecs);
                return $cropSpecs;
            }
        }

        $ratio = min($image->getWidth() / (float) $this->width, $image->getHeight() / (float) $this->height);
        $width = (int) round($ratio * $this->width);
        $height = (int) round($ratio * $this->height);
        $minx = (int) round(($image->getWidth() - $width) / 2);
        $miny = (int) round(($image->getHeight() - $height) / 2);
        return array($minx, $miny, $minx + $width, $miny + $height);
    }

    /**
     * Get min size
     *
     * @param Newscoop\Image\ImageInterface $image
     * @return array
     */
    public function getMinSize(ImageInterface $image)
    {
        list($width, $height) = NetteImage::calculateSize($image->getWidth(), $image->getHeight(), $this->width, $this->height, $this->getFlags());
        $ratio = max($width / (float) $image->getWidth(), $height / (float) $image->getHeight());
        return array($this->width, $this->height);
    }

    /**
     * Get flags
     *
     * @return int
     */
    private function getFlags()
    {
        $specs = array_shift(explode('_', $this->specs, 2));
        switch ($specs) {
            case 'fill':
            case 'crop':
                $flags = NetteImage::FILL;
                break;

            case 'fit':
            default:
                $flags = NetteImage::FIT;
                break;
        }

        return $flags;
    }

    /**
     * Test if is crop defined
     *
     * @return bool
     */
    private function isCrop()
    {
        return strpos($this->specs, 'crop') === 0;
    }
}
