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
class Rendition
{
    /**
     * @var string
     */
    private $name;

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
     * @param string $name
     * @param int $width
     * @param int $height
     * @param string $specs
     */
    public function __construct($name, $width, $height, $specs = 'crop')
    {
        $this->name = (string) $name;
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->specs = (string) $specs;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
}
