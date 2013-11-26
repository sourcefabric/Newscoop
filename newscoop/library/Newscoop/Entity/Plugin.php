<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Plugin entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\PluginRepository")
 * @ORM\Table(name="Plugins")
 */
class Plugin
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(length=256, name="Name")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", name="Details")
     * @var string
     */
    protected $details;

    /**
     * @ORM\Column(name="type", type="integer")
     * @var int
     */
    protected $type;

    /**
     * @var string to code mapper for type
     */
    static $type_enum = array('core', 'system', 'official', 'thirdparty');

    /**
     * @ORM\Column(name="installed_with", type="integer")
     * @var int
     */
    protected $installedWith;

    /**
     * @var string to code mapper for installedWith
     */
    static $installedWith_enum = array('packagist', 'zip', 'private_reposiotry');

    /**
     * @ORM\Column(type="text", name="Description")
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(length=256, name="Version")
     * @var string
     */
    protected $version;

    /**
     * @ORM\Column(length=256, name="author")
     * @var string
     */
    protected $author;

    /**
     * @ORM\Column(length=256, name="license")
     * @var string
     */
    protected $license;

    /**
     * @ORM\Column(type="boolean", length=256, name="Enabled")
     * @var boolean
     */
    protected $enabled;

    /**
     * @ORM\Column(type="datetime", name="installed_at")
     * @var string
     */
    protected $installedAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     * @var string
     */
    protected $updatedAt;

    public function __construct() {
        $this->setInstalledAt(new \DateTime());
        $this->enabled = false;
    }

    /**
     * Get id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set name
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get version
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Set version
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * Get details
     * @return string
     */
    public function getDetails() {
        return $this->details;
    }

    /**
     * Set details
     * @param string $details
     */
    public function setDetails($details) {
        $this->details = $details;

        return $this;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set description
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get author
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Set author
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = $author;

        return $this;
    }

    /**
     * Get license
     * @return string
     */
    public function getLicense() {
        return $this->license;
    }

    /**
     * Set license
     * @param string $license
     */
    public function setLicense($license) {
        $this->license = $license;

        return $this;
    }

    /**
     * Get enabled
     * @return boolean
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set enabled
     * @param boolean $enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get type
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set type
     * @param string $type
     */
    public function setType($type) {
        if (is_string($type)) {
            $type_enum = array_flip(self::$type_enum);
            $this->type = $type_enum[$type];
        } else {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Get installedWith
     * @return int
     */
    public function getInstalledWith() {
        return $this->installedWith;
    }

    /**
     * Set installedWith
     * @param string $installedWith
     */
    public function setInstalledWith($installedWith) {
        if (is_string($installedWith)) {
            $installedWith_enum = array_flip(self::$installedWith_enum);
            $this->installedWith = $installedWith_enum[$installedWith];
        } else {
            $this->installedWith = $installedWith;
        }

        return $this;
    }

    /**
     * Get install date
     *
     * @return DateTime
     */
    public function getInstalledAt()
    {
        return $this->installedAt;
    }

    /**
     * Set install date
     *
     * @param DateTime $installedAt
     */
    public function setInstalledAt(\DateTime $installedAt)
    {
        $this->installedAt = $installedAt;
        
        return $this;
    }

    /**
     * Get update date
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set update date
     *
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
}
