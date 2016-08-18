<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Newscoop\Image\ImageService;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Rendition
 * @ORM\Entity
 * @ORM\Table(name="rendition")
 */
class Rendition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $width;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $height;

    /**
     * @ORM\Column
     * @var string
     */
    protected $specs;

    /**
     * @var string
     */
    protected $coords;

    /**
     * @ORM\Column(type="integer", nullable=True)
     * @var int
     */
    protected $offset;

    /**
     * @ORM\Column(nullable=True)
     * @var string
     */
    protected $label;

    /**
     * @param int    $width
     * @param int    $height
     * @param string $specs
     * @param string $name
     * @param int    $offset
     * @param string $label
     */
    public function __construct($width, $height, $specs = 'fit', $name = null, $offset = null, $label = null)
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->specs = (string) $specs;
        $this->name = (string) $name;
        $this->offset = (int) $offset;
        $this->label = (string) $label;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set width
     *
     * @param int $width
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = (int) $width;
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
     * Set height
     *
     * @param int $height
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = (int) $height;
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
     * Set specification
     *
     * @param string $specs
     * @return void
     */
    public function setSpecs($specs)
    {
        $this->specs = (string) $specs;
    }

    /**
     * Get specification
     *
     * @return string
     */
    public function getSpecs()
    {
        return $this->coords !== null && $this->isCrop() ? 'crop_' . $this->coords :  $this->specs;
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
     * Set coordinates for image
     *
     * @param string $coords
     * @return void
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;
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
        list($width, $height) = \Newscoop\Image\ImageService::calculateSize($this->width, $this->height, $width, $height);
        return new Rendition($width, $height, $this->getSpecs());
    }

    /**
     * Get thumbnail
     *
     * @param Newscoop\Image\ImageInterface $image
     * @param Newscoop\Image\ImageService $imageService
     * @return Newscoop\Image\Thumbnail
     */
    public function getThumbnail(ImageInterface $image, ImageService $imageService)
    {
        if (!$this->fits($image)) {
            throw new \InvalidArgumentException("Image is too small.");
        }

        list($width, $height) = \Newscoop\Image\ImageService::calculateSize($image->getWidth(), $image->getHeight(), $this->width, $this->height, $this->getFlags());
        if ($this->isCrop()) {
            $width = min($width, $this->width);
            $height = min($height, $this->height);
        }

        return new Thumbnail($imageService->getSrc($image->getPath(), $this->width, $this->height, $this->getSpecs()), $width, $height);
    }

    /**
     * Generate image
     *
     * @param string $imagePath
     * @return Imagine\Gd\Image
     */
    public function generateImage($imagePath)
    {
        $path = is_file(APPLICATION_PATH . '/../' . $imagePath) ? APPLICATION_PATH . '/../' . $imagePath : $imagePath;
        $imagine = ImageService::getImagine();
        $image = $imagine->open($path);
        $imageSize = $image->getSize();

        if ($this->isCrop()) {
             $cropSpecs = explode('_', $this->getSpecs());
             if (count($cropSpecs) === 1) {
                list($width, $height) = ImageService::calculateSize($imageSize->getWidth(), $imageSize->getHeight(), $this->width, $this->height, $this->getFlags());
                $image->resize(new Box($width, $height));
                list($left, $top, $width, $height) = ImageService::calculateCutout($width, $height, '50%', '50%', $this->width, $this->height);
                $image->crop(new Point($left, $top), new Box($width, $height));
             } else {
                list(, $x0, $y0, $x1, $y1) = $cropSpecs;
                $image->crop(new Point($x0, $y0), new Box($x1 - $x0, $y1 - $y0));
                $imageSize = $image->getSize();
                list($width, $height) = ImageService::calculateSize($imageSize->getWidth(), $imageSize->getHeight(), $this->width, $this->height, $this->getFlags());
                $image->resize(new Box($width, $height));
             }
        } else {
                list($width, $height) = ImageService::calculateSize($imageSize->getWidth(), $imageSize->getHeight(), $this->width, $this->height, $this->getFlags());
                $image->resize(new Box($width, $height));
        }

        return $image;
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
            $cropSpecs = explode('_', $this->getSpecs());
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
        list($width, $height) = \Newscoop\Image\ImageService::calculateSize($image->getWidth(), $image->getHeight(), $this->width, $this->height, $this->getFlags());
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
        $originalSpecs = explode('_', $this->getSpecs(), 2);
        $specs = array_shift($originalSpecs);
        switch ($specs) {
            case 'fill':
            case 'crop':
                $flags = \Newscoop\Image\ImageService::FILL;
                break;

            case 'fit':
            default:
                $flags = \Newscoop\Image\ImageService::FIT;
                break;
        }

        return $flags;
    }

    /**
     * Test if is crop defined
     *
     * @return bool
     */
    public function isCrop()
    {
        return strpos($this->specs, 'crop') === 0;
    }

    /**
     * Test if rendition fits image
     *
     * @param ImageInterface $image
     * @return bool
     */
    public function fits(ImageInterface $image)
    {
        if (null !== $image) {
            return $this->specs === 'fit' || ($image->getWidth() >= $this->width && $image->getHeight() >= $this->height);
        }

        return false;
    }

    /**
     * Set offset
     *
     * @param int $offset
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = empty($label) ? null : (string) $label;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label !== null ? $this->label : $this->getName();
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return sprintf('%s: %s %dx%d',
            $this->getLabel(),
            $this->getSpecs(),
            $this->getWidth(),
            $this->getHeight());
    }
}
