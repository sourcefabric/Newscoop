<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * Geo Map Location Language interface
 */
interface IGeoMapLocationLanguage
{
    /**
     * Get location is enabled
     * @return bool
     */
    public function isEnabled();
}
