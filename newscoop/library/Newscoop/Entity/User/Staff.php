<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

use DateTime,
    Zend_Registry,
    Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Utils\PermissionToAcl,
    Newscoop\Entity\User,
    Newscoop\Entity\Acl\Role;

/**
 * Staff entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\User\StaffRepository")
 */
class Staff extends User implements \Zend_Acl_Role_Interface
{
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
        parent::__construct();
        $this->groups = new ArrayCollection;
        $this->reader = 'N';
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
     * Get role id
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->role ? $this->role->getId() : 0;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getParents()
    {
        return $this->getGroups();
    }

    /**
     * Check permissions
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $acl = Zend_Registry::get('acl')->getAcl($this);

        try {
            list($resource, $action) = PermissionToAcl::translate($permission);
            return $acl->isAllowed($this, strtolower($resource), strtolower($action));
        } catch (Exception $e) {
            return false;
        }
    }
}
