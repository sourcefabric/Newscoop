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

    /**
     * @param string $key
     * @param array $metadata
     */
    public function __construct($key)
    {
        $this->key = (string) $key;
        $this->cache_lifetime = 0;
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
     * Set key
     *
     * @param string $key
     * @return Newscoop\Entity\Template
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
        return $this;
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
     * Set cache lifetime
     *
     * @param int $lifetime
     * @return Newscoop\Entity\Template
     */
    public function setCacheLifetime($lifetime)
    {
        $this->cache_lifetime = abs((int) $lifetime);
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
