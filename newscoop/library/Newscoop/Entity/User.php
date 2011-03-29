<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * User entity
 * @entity
 * @table(name="liveuser_users")
 */
class User
{
    /**
     * @id @generatedValue
     * @column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @column(name="Name")
     * @var string
     */
    private $name;

    /**
     * @column(name="UName")
     * @var string
     */
    private $username;

    /**
     * @column(name="Password")
     * @var string
     */
    private $password;

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
     * Get id
     *
     * @return int
     * @deprecated
     */
    public function getUserId()
    {
        return $this->getId();
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
     * Get real name
     *
     * @return string
     * @deprecated
     */
    public function getRealName()
    {
        return $this->getName();
    }

    /**
     * Check permissions
     *
     * @param string $permission
     * @return bool
     * @deprecated
     */
    public function hasPermission($permission)
    {
        return true;
    }
}
