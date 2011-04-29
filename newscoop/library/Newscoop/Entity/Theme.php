<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Newscoop\Version;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\ThemeRepository")
 */
class Theme
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(length="80")
     * @var string
     */
    private $offset;

    /**
     * @Column(length="20")
     * @var string
     */
    private $version;

    /** @var SimpleXmlElement */
    private $config;

    /**
     * @param string $offset
     * @param SimpleXmlElement $config
     */
    public function __construct($offset, \SimpleXmlElement $config)
    {
        $this->offset = (string) $offset;
        $this->config = $config;
    }

    /**
     * Set id
     *
     * @param int $id
     * @return Newscoop\Entity\Theme
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->config['name'];
    }

    /**
     * Get offset
     *
     * @return string
     */
    public function getOffset()
    {
        return (string) $this->offset;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return (string) $this->config['version'];
    }

    /**
     * Get publisher
     *
     * @return string
     */
    public function getPublisher()
    {
        return (string) $this->config['publisher'];
    }

    /**
     * Set installed version
     *
     * @param string $version
     * @return Newscoop\Entity\Theme
     */
    public function setInstalledVersion($version = null)
    {
        if ($version === null) {
            $version = $this->getVersion();
        }

        $this->version = (string) $version;
        return $this;
    }

    /**
     * Get installed version
     *
     * @return string
     */
    public function getInstalledVersion()
    {
        return (string) $this->version;
    }

    /**
     * Get is installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return !empty($this->version);
    }

    /**
     * Get newscoop required version
     *
     * @return string
     */
    public function getNewscoopVersion()
    {
        return (string) $this->config['newscoop'];
    }

    /**
     * Get is compatible
     *
     * @return string
     */
    public function isCompatible()
    {
        return Version::compare($this->getVersion()) <= 0;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return (string) $this->config->description;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        $images = array();
        foreach ($this->config->img as $image) {
            $image['src'] = $this->offset . '/' . $image['src'];
            $images[] = $image;
        }

        return $images;
    }
}
