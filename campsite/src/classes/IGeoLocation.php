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
 * Geolocations Location interace
 */
interface IGeoLocation
{
    /**
     * Get location latitude
     * @return float
     */
    public function getLatitude();
    
    /**
     * Get location longitude
     * @return float
     */
    public function getLongitude();
    
    /**
     * Get location content
     * @param int $language
     * @return IGeoLocationContent
     */
    public function getContent($language);
}
