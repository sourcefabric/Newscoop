<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\TemplateRepository")
 * @Table(name="Templates")
 */
class Template
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @Column(length="255", name="Name")
     * @var string
     */
    private $root_path;

    /**
     * @Column(type="integer", name="CacheLifetime")
     * @var int
     */
    private $cache_lifetime;

    /** @var SplFileInfo */
    private $file;

    /**
     * @param SplFileInfo $file
     * @param string $root
     */
    public function __construct($file, $root)
    {
        $this->file = $file;
        $this->root_path = str_replace("$root/", '', $file->getPathname());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getBasename();
    }

    /**
     * @param string $name
     * @param array $args
     */
    public function __call($name, array $args)
    {
        if (!method_exists($this->file, $name)) {
            throw \BadMethodCallException($name);
        }

        return call_user_func_array(array($this->file, $name), $args);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set file
     *
     * @param SplFileInfo $file
     * @return Newscoop\Entity\Template
     */
    public function setFile(\SplFileInfo $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Newscoop\Entity\Template
     */
    public function setContent($content)
    {
        if ($this->isWritable()) {
            file_put_contents($this->getRealpath(), (string) $content);
        }

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->getRealpath());
    }

    /**
     * Set cache lifetime
     *
     * @param int $lifetime
     * @return Newscoop\Entity\Template
     */
    public function setCacheLifetime($lifetime)
    {
        $this->cache_lifetime = (int) $lifetime;
        return $this;
    }

    /**
     * Get cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return (int) $this->cache_lifetime;
    }
}
