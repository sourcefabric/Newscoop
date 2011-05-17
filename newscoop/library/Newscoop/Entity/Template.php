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
     * @var SplFileInfo
     */
    private $fileInfo;

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
     * @return string
     */
    public function __toString()
    {
        return $this->getBasename();
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
     * Get basename
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->fileInfo->getBasename();
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->fileInfo->getSize();
    }

    /**
     * Get change time
     *
     * @return DateTime
     */
    public function getChangeTime()
    {
        return new \DateTime('@' . $this->fileInfo->getCTime());
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        $pieces = explode('.', $this->key);
        if (sizeof($pieces) > 1) {
            return array_pop($pieces);
        }

        return 'file';
    }
}
