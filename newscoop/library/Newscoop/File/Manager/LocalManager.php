<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\File\Manager;

use Newscoop\Entity\Repository\TemplateRepository,
    Newscoop\Entity\Template;

/**
 * Local file manager
 */
class LocalManager implements Manager
{
    /** @var array */
    private $files = array();

    /** @var string */
    private $root;

    /** @var path */
    private $path;

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    private $repository;

    /**
     * @param string $path
     * @param string $root
     * @param Newscoop\Entity\Repository\TemplateRepository $repository
     */
    public function __construct($path, $root, TemplateRepository $repository)
    {
        $rootpath = "$root/$path";
        $realpath = realpath($rootpath);
        if (!$realpath) {
            throw new \InvalidArgumentException($rootpath);
        }

        $this->root = realpath($root);
        $this->path = str_replace("$this->root/", '', $realpath);
        $this->files = array_merge(glob("$realpath/*", GLOB_ONLYDIR), glob("$realpath/*.*")); // get sorted dirs + sorted files
        $this->repository = $repository;
    }

    /**
     * Implements Iterator::current
     *
     * @return Newscoop\Entity\Template
     */
    public function current()
    {
        $file = new \SplFileObject(current($this->files));
        return $this->repository->getTemplate($file, $this->root);
    }

    /**
     * Implements Iterator::key
     *
     * @return int
     */
    public function key()
    {
        return key($this->files);
    }

    /**
     * Implements Iterator::next
     *
     * @return void
     */
    public function next()
    {
        next($this->files);
    }

    /**
     * Implements Iterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->files);
    }

    /**
     * Implements Iterator::valid
     *
     * @return bool
     */
    public function valid()
    {
        return (bool) current($this->files);
    }

    /**
     * Implements Countable::count
     *
     * @return int
     */
    public function count()
    {
        return sizeof($this->files);
    }
}
