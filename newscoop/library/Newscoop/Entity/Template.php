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
    private $key;

    /**
     * @Column(type="integer", name="CacheLifetime")
     * @var int
     */
    private $cache_lifetime;

    /** @var array */
    private $metadata = array();

    /**
     * @param string $key
     * @param array $metadata
     */
    public function __construct($key)
    {
        $this->key = (string) $key;
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
        if (!method_exists($this->fileInfo, $name)) {
            throw \BadMethodCallException($name);
        }

        return call_user_func_array(array($this->fileInfo, $name), $args);
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
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set file info
     *
     * @param SplFileInfo $fileInfo
     * @return Newscoop\Entity\Template
     */
    public function setFileInfo(\SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
        return $this;
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
