<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

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
     * @Column(type="string", length="80", nullable=True)
     * @var string
     */
    private $first_name;

    /**
     * @Column(type="string", length="80", nullable=True)
     * @var string
     */
    private $last_name;

    /**
     * @OneToOne(targetEntity="User", inversedBy="author")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

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
     * Set user
     *
     * @param Newscoop\Entity\User $user
     * @return Newscoop\Entity\Author
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
