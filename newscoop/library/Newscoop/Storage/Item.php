<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Storage;

/**
 * Storage item
 */
class Item
{
    const TYPE_DIR = 'dir';

    /** @var string */
    private $key;

    /** @var Newscoop\Storage */
    private $storage;

    /** @var array */
    private $metadata = array();

    /**
     * @param string $key
     * @param Newscoop\Storage $storage
     * @throws InvalidArgumentException
     */
    public function __construct($key, \Newscoop\Storage $storage)
    {
        $this->key = (string) $key;
        if (empty($key)) {
            throw new \InvalidArgumentException($key);
        }

        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return basename($this->key);
    }

    /**
     * Test if item is dir
     *
     * @return bool
     */
    public function isDir()
    {
        return $this->storage->isDir($this->key);
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->getMetadata()->size;
    }

    /**
     * Get change time
     *
     * @return int
     */
    public function getChangeTime()
    {
        return $this->getMetadata()->change_time;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        if ($this->storage->isDir($this->key)) {
            return self::TYPE_DIR;
        }

        return pathinfo($this->key, PATHINFO_EXTENSION);
    }

    /**
     * Get metadata
     *
     * @return object
     */
    private function getMetadata()
    {
        if (empty($this->metadata)) {
            $this->metadata = $this->storage->fetchMetadata($this->key);
        }

        return (object) $this->metadata;
    }
}
