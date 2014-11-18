<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\View\AuthorView;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\AuthorRepository")
 * @ORM\Table(name="Authors")
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=80, nullable=True)
     * @var string
     */
    protected $first_name;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\User", mappedBy="author")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=80, nullable=True)
     * @var string
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string", length=244, nullable=True)
     * @var string
     */
    protected $email;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\AuthorType")
     * @ORM\JoinTable(name="AuthorAssignedTypes",
     *      joinColumns={
     *          @ORM\JoinColumn(name="fk_author_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="fk_type_id", referencedColumnName="id")
     *      }
     *  )
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=244, nullable=True)
     * @var string
     */
    protected $skype;

    /**
     * @ORM\Column(type="string", length=244, nullable=True)
     * @var string
     */
    protected $jabber;

    /**
     * @ORM\Column(type="string", length=244, nullable=True)
     * @var string
     */
    protected $aim;

    /**
     * @ORM\Column(type="string", nullable=True, name="biography")
     * @var string
     */
    protected $biography;

    /**
     * @ORM\Column(type="string", length=80, nullable=True)
     * @var string
     */
    protected $image;

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->first_name = (string) $firstName;
        $this->last_name = (string) $lastName;
        $this->articles = new ArrayCollection();
    }

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter for id
     *
     * @param int $id Value to set
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter for first_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Setter for first_name
     *
     * @param string $firstName Value to set
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Getter for last_name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Setter for last_name
     *
     * @param string $lastName Value to set
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }


    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return trim("$this->first_name $this->last_name");
    }

    /**
     * Getter for email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Setter for email
     *
     * @param string $email Value to set
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Getter for type
     *
     * @return Newscoop\Entity\AuthorType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for type
     *
     * @param AuthorType $type Value to set
     *
     * @return self
     */
    public function setType(\Newscoop\Entity\AuthorType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Getter for skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * Setter for skype
     *
     * @param string $skype Value to set
     *
     * @return self
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Getter for jabber
     *
     * @return string
     */
    public function getJabber()
    {
        return $this->jabber;
    }

    /**
     * Setter for jabber
     *
     * @param string $jabber Value to set
     *
     * @return self
     */
    public function setJabber($jabber)
    {
        $this->jabber = $jabber;

        return $this;
    }

    /**
     * Getter for aim
     *
     * @return string
     */
    public function getAim()
    {
        return $this->aim;
    }

    /**
     * Setter for aim
     *
     * @param aim $aim Value to set
     *
     * @return self
     */
    public function setAim($aim)
    {
        $this->aim = $aim;

        return $this;
    }


    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {

        return $this->biography;
    }

    /**
     * Set biography
     *
     * @param string biography Value to set
     *
     * @return self
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string image Value to set
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Getter for user
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Setter for user
     *
     * @param Newscoop\Entity\User $user Value to set
     *
     * @return self
     */
    public function setUser(\Newscoop\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Get view
     *
     * @return AuthorView
     */
    public function getView()
    {
        return new AuthorView(array(
            'name' => $this->getFullName(),
            'imageId' => $this->getImage()
        ));
    }
}
