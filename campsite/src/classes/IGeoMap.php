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
 * Geolocations Map interface
 */
interface IGeoMap
{
    /**
     * Get map id
     * @return int
     */
    public function getId();

    /**
     * Get map locations
     * @return array of IGeoMapLocation
     */
    public function getLocations();
}
