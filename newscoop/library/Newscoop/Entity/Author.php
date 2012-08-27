<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="Authors")
 */
class Author
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", length=80, nullable=True)
     * @var string
     */
    private $first_name;

    /**
     * @Column(type="string", length=80, nullable=True)
     * @var string
     */
    private $last_name;

    /**
     * @OneToOne(targetEntity="Newscoop\Entity\User", mappedBy="author")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\AuthorBiography", mappedBy="author")
     * @var Doctrine\Common\Collections\Collection
     */
    private $biographies;

    /**
     * @ManyToOne(targetEntity="Newscoop\Image\LocalImage")
     * @JoinColumn(name="image", referencedColumnName="Id")
     * @var Newscoop\Image\LocalImage
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
        $this->biographies = new ArrayCollection();
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
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {
        return $this->biographies->isEmpty() ? null : (string) $this->biographies->first();
    }

    /**
     * Get image
     *
     * @return Newscoop\Image\LocalImage
     */
    public function getImage()
    {
        return $this->image;
    }
}
