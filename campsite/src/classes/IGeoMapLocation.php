<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . 'IGeoLocation.php';

/**
 * Geo Map Location interace
 */
interface IGeoMapLocation extends IGeoLocation
{
    /**
     * Get content
     * @param int $language
     * @return IGeoLocationContent
     */
    public function getContent($language);
}
