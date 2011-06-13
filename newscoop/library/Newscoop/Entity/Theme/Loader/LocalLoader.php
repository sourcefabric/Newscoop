<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Theme\Loader;

use Newscoop\Entity\Theme;

/**
 * Local filesystem theme loader
 */
class LocalLoader implements Loader
{
    const CONFIG = 'theme.xml';

    /** @var string */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = realpath($path);
        if (!$this->path) {
            throw new \InvalidArgumentException("'$path' not found");
        }
    }

    /**
     * Get all themes
     *
     * @return Traversable
     */
    public function findAll()
    {
        $themes = array();
        foreach (glob($this->formatPath()) as $configFile) {
            $offset = basename(dirname($configFile));
            $config = simplexml_load_file($configFile);
            $theme = new Theme($offset, $config);
            $themes[] = $theme;
        }

        return $themes;
    }

    /**
     * Get theme
     *
     * @param string $offset
     * @return Newscoop\Entity\Theme
     */
    public function find($offset)
    {
        $configFile = $this->formatPath($offset);
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException("'$configFile' not found");
        }

        $config = simplexml_load_file($configFile);
        return new Theme($offset, $config);
    }

    /**
     * Format path for offset
     *
     * @param string $offset
     * @return string
     */
    private function formatPath($offset = '*')
    {
        return implode('/', array(
            $this->path,
            $offset,
            self::CONFIG,
        ));
    }
}
