<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Newscoop\View\AuthorView;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\AuthorRepository")
 * @ORM\Table(name="Authors")
 */
class Author
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80, nullable=True)
     * @var string
     */
    private $first_name;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\User", mappedBy="author")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=80, nullable=True)
     * @var string
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", nullable=True, name="biography")
     * @var string
     */
    private $biography;

    /**
     * @ORM\Column(type="string", length=80, nullable=True)
     * @var string
     */
    private $image;

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->first_name = (string) $firstName;
        $this->last_name = (string) $lastName;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Get types
     *
     * @return string
     */
    public function getTypes()
    {
        if (count($this->types) == 0) {
            return null;
        }

        return $this->types;
    }

    /**
     * Set types
     *
     * @return Author
     */
    public function setTypes($types)
    {
        $this->types = $types;

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
     * @return Author
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
     * @return Author
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get view
     *
     * @return Newscoop\View\AuthorView
     */
    public function getView()
    {
        return new AuthorView(array(
            'name' => $this->getFullName(),
            'imageId' => $this->getImage()
        ));
    }
}
