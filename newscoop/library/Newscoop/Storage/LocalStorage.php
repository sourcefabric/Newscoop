<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Storage;

/**
 * Local Storage
 */
class LocalStorage implements Storage
{
    const MODE = 0700; // mode for created directories

    /** @var string */
    private $root;

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = realpath($root);
        if (!$this->root) {
            throw new \InvalidArgumentException("'$root' not found");
        }
    }

    /**
     * Store item
     *
     * @param string $key
     * @param string $data
     * @return bool
     */
    public function storeItem($key, $data)
    {
        $path = $this->getPath($key);
        if (!$path) {
            return FALSE;
        }

        if (!$this->buildTree($path)) {
            return FALSE;
        }

        if (is_dir($path)) {
            return FALSE;
        }

        return (bool) file_put_contents($path, $data);
    }

    /**
     * Fetch item
     *
     * @param string $key
     * @return mixed
     */
    public function fetchItem($key)
    {
        $path = $this->getPath($key, TRUE);
        if (!$path) {
            return FALSE;
        }

        if (is_dir($path)) {
            return FALSE;
        }

        return file_get_contents($path);
    }

    /**
     * Delete item
     *
     * @param string $key
     * @return bool
     */
    public function deleteItem($key)
    {
        $path = $this->getPath($key, TRUE);
        if (!$path) {
            return FALSE;
        }

        if ($path == $this->root) {
            return FALSE;
        }

        if (is_dir($path)) {
            foreach ($this->listItems($key) as $subkey) {
                $this->deleteItem("$key/$subkey");
            }
            rmdir($path);
        } else {
            unlink($path);
        }

        return TRUE;
    }

    /**
     * Copy item
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function copyItem($from, $to)
    {
        $fromPath = $this->getPath($from, TRUE);
        if (!$fromPath) {
            return FALSE;
        }

        if (is_dir($fromPath)) {
            return FALSE;
        }

        $toPath = $this->getPath($to);
        if (!$toPath) {
            return FALSE;
        }

        $realpath = realpath($toPath);
        if ($realpath && $fromPath == $realpath) { // copy to self
            return TRUE;
        }

        if (!$this->buildTree($toPath)) {
            return FALSE;
        }

        return copy($fromPath, $toPath);
    }

    /**
     * Move item
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function moveItem($from, $to)
    {
        $fromPath = $this->getPath($from, TRUE);
        if (!$fromPath) {
            return FALSE;
        }

        $toPath = $this->getPath($to);
        if (!$toPath) {
            return FALSE;
        }

        if (!$this->buildTree($toPath)) {
            return FALSE;
        }

        return rename($fromPath, $toPath);
    }

    /**
     * List items
     *
     * @param string $key
     * @return mixed
     */
    public function listItems($key)
    {
        $path = $this->getPath($key, TRUE);
        if (!$path) {
            return array();
        }

        if (!is_dir($path)) {
            return array();
        }

        $items = array();
        foreach (glob("$path/*") as $file) {
            $items[] = basename($file);
        }

        return $items;
    }

    /**
     * Get path
     *
     * @param string $key
     * @param bool $isReal
     * @return mixed
     */
    private function getPath($key, $isRealpath = FALSE)
    {
        if (preg_match('#\.\.(/|$)#', $key)) {
            return FALSE;
        }

        $rootpath = "$this->root/$key";

        // check if is realpath
        if ($isRealpath) {
            $realpath = realpath($rootpath);
            if (!$realpath) {
                return FALSE;
            }

            return $realpath;
        }

        return $rootpath;
    }

    /**
     * Build tree for path
     *
     * @param string $path
     * @return bool
     */
    private function buildTree($path)
    {
        $dirname = dirname($path);
        if (!file_exists($dirname)) {
            if (!is_writable($this->root) || !mkdir($dirname, self::MODE, TRUE)) {
                return FALSE;
            }
        } elseif (!is_dir($dirname)) {
            return FALSE;
        }

        return TRUE;
    }
}
