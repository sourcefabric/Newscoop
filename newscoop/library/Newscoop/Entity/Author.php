<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Newscoop\View\AuthorView;

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
     * Get view
     *
     * @return Newscoop\View\AuthorView
     */
    public function getView()
    {
        return new AuthorView(array(
            'name' => $this->getFullName(),
        ));
    }
}
