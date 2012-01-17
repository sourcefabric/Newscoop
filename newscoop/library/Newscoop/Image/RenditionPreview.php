<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Rendition Preview
 */
class RenditionPreview implements RenditionInterface
{
    /**
     * @var Newscoop\Image\RenditionInterface
     */
    private $rendition;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @param Newscoop\Image\RenditionInterface $rendition
     * @param int $width
     * @param int $height
     */
    public function __construct(RenditionInterface $rendition, $width, $height)
    {
        $this->rendition = $rendition;
        if ($rendition->getWidth() <= $width && $rendition->getHeight() <= $height) { // original smaller
            $this->width = $rendition->getWidth();
            $this->height = $rendition->getHeight();
        } else if ($rendition->getHeight() <= $height) { // original width bigger
            $this->width = (int) $width;
            $this->height = round((float) $height * (float) $width / (float) $rendition->getWidth());
        } else if ($rendition->getWidth() <= $width) { // original height bigger
            $this->height = (int) $height;
            $this->width = round((float) $width * (float) $height / (float) $rendition->getHeight());
        } else {
            $ratio = min((float) $width / (float) $rendition->getWidth(), (float) $height / (float) $rendition->getHeight());
            $this->width = round((float) $ratio * (float) $rendition->getWidth());
            $this->height = round((float) $ratio * (float) $rendition->getHeight());
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->rendition;
    }

    /**
     * Get width
     *
     * @return int
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
     * Get specs
     *
     * @return string
     */
    public function getSpecs()
    {
        return $this->rendition->getSpecs();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->rendition->getName();
    }
}
