<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\TemplateRepository")
 * @ORM\Table(name="Templates")
 */
class Template
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(length=255, name="Name")
     * @var string
     */
    private $key;

    /**
     * @ORM\Column(type="integer", name="CacheLifetime")
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

    /* ----- LEGACY ----- */
	/**
     * Check if the template exists
     *
     * @return bool
     *          True always for now
     */
    public function exists()
    {
        return true;
    }
}
