<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aliases entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="Aliases")
 */
class Aliases
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="Name")
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="\Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var \Newscoop\Entity\Publication
     */
    protected $publication;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get publication
     *
     * @return \Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Sets the value of publication.
     *
     * @param \Newscoop\Entity\Publication $publication the publication
     *
     * @return self
     */
    public function setPublication(\Newscoop\Entity\Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }
}
