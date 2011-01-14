<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * Geo Multimedia interface
 */
interface IGeoMultimedia
{
    /**
     * Get type
     * @return string
     */
    public function getType();

    /**
     * Get spec
     * @return string
     */
    public function getSpec();

    /**
     * Get src
     * @return string
     */
    public function getSrc();

    /**
     * Get width
     * @return int
     */
    public function getWidth();

    /**
     * Get height
     * @return int
     */
    public function getHeight();

}
