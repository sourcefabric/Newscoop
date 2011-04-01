<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Entity\Acl\Role;

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
     * @column(name="EMail")
     * @var string
     */
    private $email;

    /**
     * @column(type="datetime", name="time_created")
     * @var DateTime
     */
    private $timeCreated;

    /**
     * @manyToMany(targetEntity="Newscoop\Entity\User\Group")
     * @joinTable(name="liveuser_groupusers",
     *      joinColumns={@joinColumn(name="perm_user_id", referencedColumnName="Id")},
     *      inverseJoinColumns={@joinColumn(name="group_id", referencedColumnName="group_id")}
     *      )
     */
    private $groups;

    /**
     * @oneToOne(targetEntity="Newscoop\Entity\Acl\Role")
     * @var Newscoop\Entity\Acl\Role
     */
    private $role;

    /**
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection;
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
     * Get user name
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get time created
     *
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * Get groups
     *
     * @return array of Newscoop\Entity\User\Group
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set role
     *
     * @param Newscoop\Entity\Acl\Role $role
     * @return Newscoop\Entity\User
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return Newscoop\Entity\Acl\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get role id
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->role ? $this->role->getId() : 0;
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
