<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Image Interface
 */
interface ImageInterface
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();
}
