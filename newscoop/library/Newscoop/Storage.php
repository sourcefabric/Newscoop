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
    // error codes
    const ERROR_NOT_FOUND = 1;
    const ERROR_NOT_DIR = 2;
    const ERROR_NOT_FILE = 3;
    const ERROR_NOT_EMPTY = 4;
    const ERROR_CONFLICT = 5;
    const ERROR_KEY_INVALID = 6;

    /** @var string */
    private $root; 
    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = realpath($root);

        if (!$this->root) {
            throw new \InvalidArgumentException($root, self::ERROR_NOT_FOUND);
        }

        if (!is_dir($this->root)) {
            throw new \InvalidArgumentException($root, self::ERROR_NOT_DIR);
        }
    }

    /**
     * Store item
     *
     * @param string $dest
     * @param string $data
     * @return int
     * @throws InvalidArgumentException
     */
    public function storeItem($dest, $data)
    {
        $path = $this->getPath($dest);
        $realpath = realpath($path);
        if ($realpath && !is_file($realpath)) {
            throw new \InvalidArgumentException($dest, self::ERROR_NOT_FILE);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(dirname($dest), self::ERROR_NOT_DIR);
        }

        return file_put_contents($path, $data);
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
            return null;
        }

        if (is_dir($path)) {
            throw new \InvalidArgumentException($key, self::ERROR_NOT_FILE);
        }

        return file_get_contents($path);
    }

    /**
     * Delete item
     *
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public function deleteItem($key)
    {
        $path = $this->getPath($key, TRUE);
        if (!$path) {
            throw new \InvalidArgumentException($key, self::ERROR_NOT_FOUND);
        }

        if (is_dir($path)) {
            foreach ($this->listItems($key) as $item) {
                throw new \InvalidArgumentException($key, self::ERROR_NOT_EMPTY);
            }

            rmdir($path);
        } else {
            unlink($path);
        }
    }

    /**
     * Copy item
     *
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function copyItem($src, $dest)
    {
        $srcPath = $this->getPath($src, TRUE);
        if (!$srcPath) {
            throw new \InvalidArgumentException($src, self::ERROR_NOT_FOUND);
        }

        if (is_dir($srcPath)) {
            throw new \InvalidArgumentException($src, self::ERROR_NOT_FILE);
        }

        $dir = dirname($srcPath);
        $destPath = "$dir/" . basename($dest);
        if (realpath($destPath)) {
            throw new \InvalidArgumentException($dest, self::ERROR_CONFLICT);
        }

        return copy($srcPath, $destPath);
    }

    /**
     * Move item
     *
     * @param string $src
     * @param string $dest
     * @return void
     * @throws InvalidArgumentException
     */
    public function moveItem($src, $dest)
    {
        $srcPath = $this->getPath($src, TRUE);
        if (!$srcPath || !is_file($srcPath)) { // src not found or !file
            throw new \InvalidArgumentException($src);
        }

        $destPath = $this->getPath($dest, TRUE);
        if (!$destPath || !is_dir($destPath)) { // dest not found or !dir
            throw new \InvalidArgumentException($dest);
        }

        $name = basename($srcPath);
        $destName = "$destPath/$name";
        if (realpath($destName)) { // dest/name exists
            throw new \InvalidArgumentException("$dest/$name");
        }

        rename($srcPath, $destName);
        $this->replace($src, "$dest/$name");
    }

    /**
     * Rename item
     *
     * @param string $src
     * @param string $dest
     * @return void
     * @throws InvalidArgumentException
     */
    public function renameItem($src, $dest)
    {
        $srcPath = $this->getPath($src, TRUE);
        if (!$srcPath) {
            throw new \InvalidArgumentException($src, self::ERROR_NOT_FOUND);
        }

        if (!is_file($srcPath)) {
            throw new \InvalidArgumentException($src, self::ERROR_NOT_FILE);
        }

        $dir = dirname($srcPath);
        $destPath = "$dir/" . basename($dest);
        if (realpath($destPath)) {
            throw new \InvalidArgumentException($dest, self::ERROR_CONFLICT);
        }

        rename($srcPath, $destPath);
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
            throw new \InvalidArgumentException($key, self::ERROR_NOT_FOUND);
        }

        if (!is_dir($path)) {
            throw new \InvalidArgumentException($key, self::ERROR_NOT_DIR);
        }

        $items = array();
        foreach (glob("$path/*") as $file) {
            $items[] = basename($file);
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
        if (realpath($path) || !mkdir($path)) {
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
     * Get storage item
     *
     * @param string $key
     * @return Newscoop\StorageItem
     */
    public function getItem($key)
    {
        return new Item($key, $this);
    }

    /**
     * Get mime type
     *
     * @param string $key
     * @return string
     */
    public function getMimeType($key)
    {
        $realpath = realpath("$this->root/$key");
        $finfo = new \finfo(FILEINFO_MIME);
        return $finfo->file($realpath);
    }

    /**
     * Get realpath
     *
     * @param string $key
     * @return string
     */
    public function getRealpath($key)
    {
        return realpath("$this->root/$key");
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
        $key = ltrim($key, ' /');
        if (preg_match('#\.\.(/|$)#', $key)) {
            throw new \InvalidArgumentException($key, self::ERROR_KEY_INVALID);
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
     * Replace key in storage
     *
     * @param string $old
     * @param string $new
     * @param object $replaceEngine
     * @return void
     */
    private function replace($old, $new, $replaceEngine = null)
    {
        if (!isset($replaceEngine)) {
		    $replaceEngine = new \FileTextSearch();
        }

		$replaceEngine->setExtensions(array('tpl', 'css'));
		$replaceEngine->setSearchKey($old);
		$replaceEngine->setReplacementKey($new);
		$replaceEngine->findReplace($this->root);

		$tpl1_name = $old;
		$tpl2_name = $new;
		if (pathinfo($old, PATHINFO_EXTENSION) == 'tpl') {
			$tpl1_name = ' ' . $old;
			$tpl2_name = ' ' . $new;
		}

		$replaceEngine->setSearchKey($tpl1_name);
		$replaceEngine->setReplacementKey($tpl2_name);
		$replaceEngine->findReplace($this->root);
    }
}
