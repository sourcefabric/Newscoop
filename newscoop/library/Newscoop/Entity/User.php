<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Utils\PermissionToAcl,
    Newscoop\Entity\Acl\Role;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\UserRepository")
 * @Table(name="liveuser_users")
 */
class User implements \Zend_Acl_Role_Interface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;
    const STATUS_DELETED = 3;

    const HASH_SEP = '$';
    const HASH_ALGO = 'sha1';

    /**
     * @Id @GeneratedValue
     * @Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", length="80", name="UName")
     * @var string
     */
    private $username;

    /**
     * @Column(type="string", length="60", name="Password", nullable=TRUE)
     * @var string
     */
    private $password;

    /**
     * @Column(type="string", length="80", name="Name", nullable=TRUE)
     * @var string
     */
    private $first_name;

    /**
     * @Column(type="string", length="80", name="Field1", nullable=TRUE)
     * @var string
     * @todo add new db column
     */
    private $last_name;

    /**
     * @Column(name="EMail", length="80", nullable=TRUE)
     * @var string
     */
    private $email;

    /**
     * @Column(type="datetime", name="time_created")
     * @var DateTime
     */
    private $created;

    /**
     * @Column(type="integer", length="1", name="Field2", nullable=TRUE)
     * @var int
     * @todo add new db column
     */
    private $status;

    /**
     * @Column(type="boolean", name="Field3")
     * @var bool
     * @todo add new db column
     */
    private $is_admin;

    /**
     * @Column(type="boolean", name="Field4")
     * @var bool
     * @todo add new db column
     */
    private $is_public;

    /**
     * @oneToOne(targetEntity="Newscoop\Entity\Acl\Role", cascade={"ALL"})
     * @var Newscoop\Entity\Acl\Role
     */
    private $role;

    /**
     * @manyToMany(targetEntity="Newscoop\Entity\User\Group")
     * @joinTable(name="liveuser_groupusers",
     *      joinColumns={@joinColumn(name="perm_user_id", referencedColumnName="Id")},
     *      inverseJoinColumns={@joinColumn(name="group_id", referencedColumnName="group_id")}
     *      )
     * @var Doctrine\Common\Collections\Collection;
     */
    private $groups;

    /**
     * @OneToMany(targetEntity="UserAttribute", mappedBy="user", cascade={"ALL"}, indexBy="attribute")
     * @var Doctrine\Common\Collections\Collection;
     */
    private $attributes;

    /**
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->groups = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->status = self::STATUS_ACTIVE; // @todo change to inactive once email confirmation works
        $this->role = new Role();
        $this->setPassword($this->generateRandomString(6)); // make sure password is not empty
        $this->is_admin = FALSE;
        $this->is_public = FALSE;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->getId();
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Newscoop\Entity\User
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return (string) $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Newscoop\Entity\User
     */
    public function setPassword($password)
    {
        $salt = $this->generateRandomString();
        $this->password = implode(self::HASH_SEP, array(
            self::HASH_ALGO,
            $salt,
            hash(self::HASH_ALGO, $salt . $password),
        ));

        return $this;
    }

    /**
     * Check password
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        if (sizeof(explode(self::HASH_SEP, $this->password)) != 3) { // fallback
            if ($this->password == sha1($password)) { // update old password on success
                $this->setPassword($password);
                return TRUE;
            }

            return FALSE;
        }

        list($algo, $salt, $password_hash) = explode(self::HASH_SEP, $this->password);
        return $password_hash === hash($algo, $salt . $password);
    }

    /**
     * Get random string
     *
     * @param int $length
     * @param string $allowed_chars
     * @return string
     */
    final protected function generateRandomString($length = 12, $allowed_chars = 'abcdefghijklmnopqrstuvwxyzAMCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $allowed_chars[mt_rand(0, strlen($allowed_chars) - 1)];
        }

        return $return;
    }

    /**
     * Set first name
     *
     * @param string $first_name
     * @return Newscoop\Entity\User
     */
    public function setFirstName($first_name)
    {
        $this->first_name = (string) $first_name;
        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return (string) $this->first_name;
    }

    /**
     * Set last name
     *
     * @param string $last_name
     * @return Newscoop\Entity\User
     */
    public function setLastName($last_name)
    {
        $this->last_name = (string) $last_name;
        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return (string) $this->last_name;
    }

    /**
     * Get real name
     *
     * @return string
     */
    public function getRealName()
    {
        return (string)  $this->first_name.' '.$this->last_name;
    }



    /**
     * Set status
     *
     * @param int $status
     * @return Newscoop\Entity\User
     */
    public function setStatus($status)
    {
        static $statuses = array(
            self::STATUS_INACTIVE,
            self::STATUS_ACTIVE,
            self::STATUS_BANNED,
            self::STATUS_DELETED,
        );

        if (!in_array($status, $statuses)) {
            throw new \InvalidArgumentException("Unknown status '$status'");
        }

        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }

    /**
     * Test if user is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Newscoop\Entity\User
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
        return $this;
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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set admin switch
     *
     * @param bool $admin
     * @return Newscoop\Entity\User
     */
    public function setAdmin($admin)
    {
        $this->is_admin = (bool) $admin;
        return $this;
    }

    /**
     * Test if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }

    /**
     * Set user is public
     *
     * @param bool $public
     * @return Newscoop\Entity\User
     */
    public function setPublic($public)
    {
        $this->is_public = (bool) $public;
        return $this;
    }

    /**
     * Test if user is public
     *
     * @return bool
     */
    public function isPublic()
    {
        return (bool) $this->is_public;
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
     * Add attribute
     *
     * @param string $name
     * @param string $value
     * @return Newscoop\Entity\User
     */
    public function addAttribute($name, $value)
    {
        if (empty($this->attributes[$name])) {
            $this->attributes[$name] = new UserAttribute($name, $value, $this);
        } else {
            $this->attributes[$name]->setValue($value);
        }

        return $this;
    }

    /**
     * Get attribute
     *
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name]->getValue();
        }

        return null;
    }

    /**
     * Check permissions
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $acl = \Zend_Registry::get('acl')->getAcl($this);
        try {
            list($resource, $action) = PermissionToAcl::translate($permission);
            if($acl->isAllowed($this, strtolower($resource), strtolower($action))) {
				return \SaaS::singleton()->hasPermission($permission);
            } else {
            	return FALSE;
            }
        } catch (\Exception $e) {
            return FALSE;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }
}
