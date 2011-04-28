<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\ThemeRepository")
 * @Table(name="theme")
 */
class Theme
{
    /**
     * @Id
     * @Column(length="80")
     * @var string
     */
    private $id;

    /**
     * @Column(length="20", name="version")
     * @var string
     */
    private $installedVersion;

    /** @var SimpleXmlElement */
    private $config;

    /**
     * @param string $id
     * @param SimpleXmlElement $config
     */
    public function __construct($id, \SimpleXmlElement $config)
    {
        $this->id = (string) $id;
        $this->config = $config;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return (string) $this->id;
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
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return (string) $this->config['version'];
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

        $this->installedVersion = (string) $version;
        return $this;
    }

    /**
     * Get installed version
     *
     * @return string
     */
    public function getInstalledVersion()
    {
        return (string) $this->installedVersion;
    }

    /**
     * Get is installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return !empty($this->installedVersion);
    }
}
