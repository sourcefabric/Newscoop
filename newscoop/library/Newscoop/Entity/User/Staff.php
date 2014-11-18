<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

use Zend_Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Newscoop\Utils\PermissionToAcl;
use Newscoop\Entity\User;
use Newscoop\Entity\Acl\Role;

/**
 * Staff entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\User\StaffRepository")
 */
class Staff extends User implements \Zend_Acl_Role_Interface
{
    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\User\Group")
     * @ORM\JoinTable(name="liveuser_groupusers",
     *      joinColumns={@ORM\JoinColumn(name="perm_user_id", referencedColumnName="Id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="group_id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Acl\Role")
     * @var Newscoop\Entity\Acl\Role
     */
    protected $role;

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
     * @param Role $role
     * 
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
    public function hasPermission($permission, $resource = null, $action = null)
    {
        $acl = Zend_Registry::get('acl')->getAcl($this);
        try {
            list($resource, $action) = PermissionToAcl::translate($permission);
            if ($acl->isAllowed($this, strtolower($resource), strtolower($action))) {
				return \SaaS::singleton()->hasPermission($permission);
            } else {
            	return FALSE;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
