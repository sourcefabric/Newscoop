<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Newscoop\Storage\Item;

/**
 * Storage
 */
class Storage
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
            throw new \InvalidArgumentException("'$root' not found"); }
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
            foreach ($this->listItems($key) as $item) {
                $this->deleteItem($item->getKey());
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

        $realpath = realpath($toPath);
        if (is_dir($realpath)) { // move into dir
            $toPath .= '/' . basename($fromPath);
        } elseif ($realpath && is_dir($fromPath)) { // folder to file
            return FALSE;
        }

        return rename($fromPath, $toPath);
    }

    /**
     * Rename item
     *
     * @param string $form
     * @param string $to
     * @return bool
     */
    public function renameItem($from, $to)
    {
        $fromPath = $this->getPath($from, TRUE);
        if (!$fromPath) {
            return FALSE;
        }

        $toPath = $this->getPath($to);
        if (!$toPath) {
            return FALSE;
        }

        $realpath = realpath($toPath);

        if (!is_dir($fromPath) && is_dir($toPath)) { // file to dir
            return FALSE;
        }

        if (is_dir($fromPath) && $realpath) { // dir to dir
            return FALSE;
        }

        if (!realpath(dirname($toPath))) {
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
        if (!$path || !is_dir($path)) {
            return array();
        }

        $items = array();
        foreach (glob("$path/*") as $file) {
            $key = trim(str_replace($this->root, '', $file), '/');
            $items[] = new Item($key, $this);
        }

        return $items;
    }

    /**
     * Test if item is dir
     *
     * @param string $key
     * @return bool
     */
    public function isDir($key)
    {
        return is_dir("$this->root/$key");
    }

    /**
     * Fetch item metadata
     *
     * @param string $key
     * @return array
     */
    public function fetchMetadata($key)
    {
        $info = new \SplFileInfo("$this->root/$key");
        return array(
            'size' => $info->getSize(),
            'change_time' => $info->getCTime(),
        );
    }

    /**
     * Test is writable
     *
     * @param string $key
     * @return bool
     */
    public function isWritable($key)
    {
        $info = new \SplFileInfo("$this->root/$key");
        return $info->isWritable();
    }

    /**
     * Create dir
     *
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public function createDir($key)
    {
        $path = $this->getPath($key);
        if (realpath($path) || !mkdir($path, self::MODE)) {
            throw new \InvalidArgumentException($key);
        }
    }

    /**
     * Create file
     *
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public function createFile($key)
    {
        $path = $this->getPath($key);
        if (realpath($path) || !touch($path)) {
            throw new \InvalidArgumentException($key);
        }
    }

    /**
     * Test is used
     *
     * @param string $key
     * @param object $searchEngine
     * @return mixed
     */
    public function isUsed($key, $searchEngine = null)
    {
        if (!isset($searchEngine)) {
            $searchEngine = new \FileTextSearch();
        }

        $searchEngine->setExtensions(array('tpl', 'css'));
        $searchEngine->setSearchKey($key);

        $result = $searchEngine->findReplace($this->root);
        if (is_array($result) && sizeof($result) > 0) {
            return $result[0];
        }

        if (pathinfo($key, PATHINFO_EXTENSION) == 'tpl') {
            $key = " $key";
        }

        $searchEngine->setSearchKey($key);
        $result = $searchEngine->findReplace($this->root);
        if (is_array($result) && sizeof($result) > 0) {
            return $result[0];
        }

        return $searchEngine->m_totalFound > 0;
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
