<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="user_attribute")
 */
class UserAttribute
{
    /**
     * @Id @ManyToOne(targetEntity="User", inversedBy="attributes")
     * @JoinColumn(name="user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @Id @Column(type="string", length="255")
     * @var string
     */
    private $attribute;

    /**
     * @Column(type="string", length="255")
     * @var string
     */
    private $value;

    /**
     * @param string $name
     * @param string $value
     * @param Newscoop\Entity\User $user
     */
    public function __construct($name, $value, User $user)
    {
        $this->attribute = (string) $name;
        $this->value = (string) $value;
        $this->user = $user;
    }

    /**
     * Get attribute value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
