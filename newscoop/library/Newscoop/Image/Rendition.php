<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Rendition
 */
class Rendition implements RenditionInterface
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
}
