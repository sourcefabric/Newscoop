<?php

/**
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop;

use SWP\UpdaterBundle\Version\VersionInterface;

/**
 * Vesrions class.
 */
class Version implements VersionInterface
{
    const VERSION = '4.4.6';

    const API_VERSION = '1.2';

    private $version = self::VERSION;

    /**
     * Compare version with current Newscoop version.
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

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}
