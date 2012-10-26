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
    const HTTP_USER_AGENT = 'http_user_agent';
    const IS_VERIFIED = 'is_verified';

    /**
     * @Id @ManyToOne(targetEntity="User", inversedBy="attributes")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @Id @Column(type="string", length=255)
     * @var string
     */
    private $attribute;

    /**
     * @Column(type="string", length=255, nullable=True)
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
        $this->value = $value;
        $this->user = $user;
    }

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->attribute;
    }

    /**
     * Set attribute value
     *
     * @param mixed $value
     * @return Newscoop\Entity\UserAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
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
