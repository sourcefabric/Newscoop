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
 * Geolocations Location Content interace
 */
interface IGeoLocationContent
{
    /**
     * Get name
     * @return string
     */
    public function getName();

    /**
     * Get content
     * @return string
     */
    public function getContent();
}
