<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Storage;

use Zend_Cloud_StorageService_Adapter as Adapter;

/**
 * Storage Service
 */
class StorageService
{
    /**
     * @param Zend_Cloud_StorageService_Adapter
     */
    protected $adapter;

    /**
     * @param Zend_Cloud_StorageService_Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Move given image into new location
     *
     * @param string $path
     * @return string
     */
    public function moveImage($path)
    {
        return $this->moveFile($path, 'images');
    }

    /**
     * Move given thumbnail into new location
     *
     * @param string $path
     * @return string
     */
    public function moveThumbnail($path)
    {
        return $this->moveFile($path, 'images/thumbnails');
    }

    /**
     * Move file in given path to storage starting at root
     *
     * @param string $path
     * @param string $root
     * @return string
     */
    private function moveFile($path, $root)
    {
        $hash = sha1($this->adapter->fetchItem($path));
        $name = sprintf(
            '%s/%s/%s.%s',
            substr($hash, 0, 1),
            substr($hash, 1, 2),
            $hash,
            pathinfo($path, PATHINFO_EXTENSION)
        );

        $dest = trim($root, '/') . '/' . $name;
        if (is_string($this->adapter->getClient())) {
            $dir = $this->adapter->getClient() . '/' . dirname($dest);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $this->adapter->moveItem($path, $dest);
        return $name;
    }
}
