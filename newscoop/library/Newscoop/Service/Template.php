<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Storage,
    Newscoop\Entity\Repository\TemplateRepository;

/**
 * Template service
 */
class Template
{
    /** @var array */
    private static $equivalentMimeTypes = array(
        'text/plain',
        'text/html',
        'application/x-php',
        'application/octet-stream',
        'application/javascript',
        'text/x-c',
        'text/css' ,
        'text/x-php',
        'application/x-httpd-php',
        'text/x-c++',
        'application/x-empty; charset=binary',
    );

    /** @var Newscoop\Storage */
    private $storage;

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    private $repository;

    /**
     * @param Newscoop\Storage $storage
     * @param Newscoop\Entity\Repository\TemplateRepository $repository
     */
    public function __construct(Storage $storage, TemplateRepository $repository)
    {
        $this->storage = $storage;
        $this->repository = $repository;
    }

    /**
     * Find items
     *
     * @param string $path
     * @return Iterator
     */
    public function listItems($path)
    {
        try {
            $items = array();
            foreach ($this->storage->listItems($path) as $file) {
                $items[] = $this->fetchMetadata("$path/$file");
            }

            return $items;
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS("'$1' not found", $path), $e->getCode(), $e);
        }
    }

    /**
     * Store metadata
     *
     * @param string $key
     * @param array $metadata
     * @return void
     */
    public function storeMetadata($key, array $metadata)
    {
        $template = $this->repository->getTemplate($key);
        $this->repository->save($template, $metadata);
    }

    /**
     * Fetch item metadata
     *
     * @param string $key
     * @return object
     */
    public function fetchMetadata($key)
    {
        $item = $this->storage->getItem($key);
        $metadata = array(
            'key' => $item->getKey(),
            'name' => $item->getName(),
            'type' => $item->getType(),
            'realpath' => $this->storage->getRealpath($key),
        );

        if (!$item->isDir()) {
            $template = $this->repository->getTemplate($key);
            $metadata += array(
                'size' => $item->getSize(),
                'ctime' => $item->getChangeTime(),

                'id' => $template->getId(),
                'ttl' => $template->getCacheLifetime(),
            );
        }

        return (object) $metadata;
    }

    /**
     * Store item
     *
     * @param string $key
     * @param string $data
     * @return void
     */
    public function storeItem($key, $data)
    {
        $this->storage->storeItem($key, $data);
    }

    /**
     * Fetch item
     *
     * @param string $key
     * @return string
     */
    public function fetchItem($key)
    {
        return $this->storage->fetchItem($key);
    }

    /**
     * Replace item
     *
     * @param string $key
     * @param Zend_Form_Element_File $file
     * @return void
     * @throws InvalidArgumentException
     */
    public function replaceItem($key, \Zend_Form_Element_File $file)
    {
        $oldMime = current(explode(';', $this->storage->getMimeType($key)));
        $newMime = current(explode(';', $file->getMimeType()));

        if ($oldMime != $newMime && !(in_array($oldMime, self::$equivalentMimeTypes) && in_array($newMime, self::$equivalentMimeTypes))) {
            throw new \InvalidArgumentException(getGS('You can only replace a file with a file of the same type.  The original file is of type "$1", and the file you uploaded was of type "$2".', $oldMime, $newMime));
        }

        $this->storage->storeItem($key, file_get_contents($file->getFileName()));
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
        if ($this->repository->isUsed($key) || $this->storage->isUsed($key)) {
		    throw new \InvalidArgumentException(getGS("The template object $1 is in use and can not be deleted.", $key));
        }

        try {
            $this->storage->deleteItem($key);
            $this->repository->delete($key);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS("Can't remove non empty directory '$1'", basename($key)), $e->getCode(), $e);
        }
    }

    /**
     * Copy item
     *
     * @param string $src
     * @param string $dest
     * @return void
     * @throws InvalidArgumentException
     */
    public function copyItem($src, $dest)
    {
        try {
            $this->storage->copyItem($src, $dest);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS('The template $1 could not be created.', "<strong>$dest</strong>"), $e->getCode(), $e);
        }
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
        try {
            $name = basename($src);
            $this->storage->moveItem($src, $dest);
            $this->repository->updateKey($src, "$dest/$name");
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS("Can't move file $1.", $name), $e->getCode(), $e);
        }
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
        try {
            $name = basename($dest);
            $dest = ltrim(dirname($src) . $name, './');
            $this->storage->renameItem($src, $name);
            $this->repository->updateKey($src, $dest);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS('The template object $1 could not be renamed.', basename($src)), $e->getCode(), $e);
        }
    }

    /**
     * Create file
     *
     * @param string $name
     * @return void
     * @throws InvalidArgumentException
     */
    public function createFile($name)
    {
        try {
            $this->storage->createFile($name);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS('A file or folder having the name $1 already exists', $name), $e->getCode(), $e);
        }
    }

    /**
     * Create folder
     *
     * @param string $name
     * @return void
     * @throws InvalidArgumentException
     */
    public function createFolder($name)
    {
        try {
            $this->storage->createDir($name);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(getGS('A file or folder having the name $1 already exists', $name), $e->getCode(), $e);
        }
    }

    /**
     * Test is writable
     *
     * @param string $dest
     * @return bool
     */
    public function isWritable($dest)
    {
        return $this->storage->isWritable($dest);
    }
}
