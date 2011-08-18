<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 */
class UserAttribute
{
    /**
     * @Id @ManyToOne(targetEntity="User", inversedBy="attributes")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @Id @Column(type="string")
     * @var string
     */
    private $attribute;

    /**
     * @Column(type="string")
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
        $this->attribute = $name;
        $this->value = $value;
        $this->user = $user;
    }
}
