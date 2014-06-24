<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="OAuthClient")
 * @ORM\Entity()
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="\Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $publication;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\User", mappedBy="clients", cascade={"remove"})
     * @var array
     */
    protected $users;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $trusted;

    /**
     * Construct Client object
     */
    public function __construct()
    {
        parent::__construct();
        $this->users = new ArrayCollection();
        $this->trusted = false;
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of publication.
     *
     * @return Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Sets the value of publication.
     *
     * @param Newscoop\Entity\Publication $publication the publication
     *
     * @return self
     */
    public function setPublication(\Newscoop\Entity\Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrisString()
    {
        return implode(', ', $this->redirectUris);
    }

    /**
     * Add User to client
     *
     * @param Newscoop\Entity\User $user
     *
     * @return self
     */
    public function addUser(\Newscoop\Entity\User $user)
    {
        $this->users->add($user);

        return $this;
    }

    /**
     * Get all Client users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Gets trusted status.
     *
     * @return boolean
     */
    public function getTrusted()
    {
        return $this->trusted;
    }

    /**
     * Sets trusted status.
     *
     * @param boolean $trusted App status
     *
     * @return self
     */
    public function setTrusted($trusted)
    {
        $this->trusted = $trusted;

        return $this;
    }
}
