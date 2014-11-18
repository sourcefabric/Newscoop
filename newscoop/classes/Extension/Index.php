<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/File.php';

/**
 * Extension Index
 */
class Extension_Index
{
    /** @var array */
    private $dirs = array();

    /**
     * Add directory (or directories) to be indexed
     * @param string $dirs
     * @return Extension_Index
     */
    public function addDirectory($dirs)
    {
        foreach ((array) $dirs as $dir) {
            $real = realpath($dir);
            if ($real === FALSE) {
                throw new InvalidArgumentException("Directory '$dir' not found.");
            } elseif (!in_array($dir, $this->dirs)) {
                $this->dirs[] = $dir;
            }
        }
        return $this;
    }

    /**
     * Get directories
     * @return array
     */
    public function getDirs()
    {
        return $this->dirs;
    }

    /**
     * Scan directory for files
     * @param string $pattern
     * @return array
     */
    public function getFiles($pattern)
    {
        $files = array();
        foreach ($this->dirs as $dir) {
            foreach (glob("$dir/$pattern") as $path) {
                $files[] = $path;
            }
        }
        return $files;
    }

    /**
     * Find all implementatios of given interface
     * @param string $interface
     * @param string $pattern
     * @return array
     */
    public function find($interface, $pattern = '*/*.php')
    {
        // index files
        $extensions = array();
        foreach ($this->getFiles($pattern) as $path) {
            $file = new Extension_File($path);
            $extensions = array_merge($extensions, $file->find($interface));
        }
        return $extensions;
    }
}
