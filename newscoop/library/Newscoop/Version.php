<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Vesrions class
 */
class Version
{
    const VERSION = '4.3.0-alpha';

    const API_VERSION = '1.1';

    /**
     * Compare version with current Newscoop version
     *
     * @param string $version
     *
     * @return int
     */
    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}
