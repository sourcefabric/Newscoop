<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Rendition Interface
 */
interface RenditionInterface
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get width
     *
     * @return void
     */
    public function getWidth();

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight();

    /**
     * Get specification
     *
     * @return string
     */
    public function getSpecs();
}
