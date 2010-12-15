<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/IGeoLocation.php';

/**
 * Geo Map Location interface
 */
interface IGeoMapLocation extends IGeoLocation
{
    /**
     * Get id
     * @return int
     */
    public function getId();

    /**
     * Get content
     * @param int $language
     * @return IGeoMapLocationContent
     */
    public function getContent($language);
}
