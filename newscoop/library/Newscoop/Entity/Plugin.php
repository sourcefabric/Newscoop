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
    private $id;

    /**
     * @ORM\Column(length=256, name="Name")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="Text", name="Description")
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(length=256, name="Version")
     * @var string
     */
    protected $version;

    /**
     * @ORM\Column(length=256, name="Enabled")
     * @var string
     */
    protected $enabled;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }
}
