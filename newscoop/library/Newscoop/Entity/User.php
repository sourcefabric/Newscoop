<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Utils\PermissionToAcl;
use Newscoop\Entity\Acl\Role;
use Newscoop\Entity\User\Group;
use Newscoop\Entity\Author;
use Newscoop\View\UserView;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Zend_View_Abstract;
use Newscoop\Search\DocumentInterface;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\UserRepository")
 * @ORM\Table(name="liveuser_users", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="username_idx", columns={"Uname"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class User implements \Zend_Acl_Role_Interface, UserInterface, \Serializable, EquatableInterface, DocumentInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;
    const STATUS_DELETED = 3;

    const HASH_SEP = '$';
    const HASH_ALGO = 'sha1';

    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=80, name="EMail")
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=80, nullable=TRUE, name="UName")
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=60, nullable=TRUE, name="Password")
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=80, nullable=TRUE, name="Name")
     * @var string
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string", length=80, nullable=TRUE)
     * @var string
     */
    protected $last_name;

    /**
     * @ORM\Column(type="datetime", name="time_created")
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="time_updated", nullable=true)
     * @var DateTime
     */
    protected $updated;

    /**
     * @ORM\Column(type="datetime", name="lastLogin", nullable=true)
     * @var DateTime
     */
    protected $lastLogin;

    /**
     * @ORM\Column(type="integer", length=1)
     * @var int
     */
    protected $status = self::STATUS_INACTIVE;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $is_admin;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $is_public;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $points;

    /**
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     * @var string
     */
    protected $image;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Acl\Role", cascade={"ALL"})
     * @var Newscoop\Entity\Acl\Role
     */
    protected $role;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\User\Group", inversedBy="users")
     * @ORM\JoinTable(name="liveuser_groupusers",
     *      joinColumns={@ORM\JoinColumn(name="perm_user_id", referencedColumnName="Id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="group_id")}
     *      )
     * @var Doctrine\Common\Collections\Collection;
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="UserAttribute", mappedBy="user", cascade={"ALL"}, indexBy="attribute")
     * @var Doctrine\Common\Collections\Collection;
     */
    protected $attributes;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Comment\Commenter", mappedBy="user", cascade={"ALL"}, indexBy="name")
     * @var Doctrine\Common\Collections\Collection;
     */
    protected $commenters;

    /**
     * @ORM\Column(type="integer", name="password_reset_token", length=85, nullable=True)
     * @var string
     */
    protected $resetToken;

    /**
     * @ORM\Column(type="string", nullable=True)
     * @var int
     */
    protected $subscriber;

    /**
     * @ORM\OneToOne(targetEntity="Author", inversedBy="user")
     * @var Newscoop\Entity\Author
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime", nullable=True)
     * @var DateTime
     */
    protected $indexed;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\UserIdentity", mappedBy="user", cascade={"remove"})
     * @var Doctrine\Common\Collections\Collection
     */
    protected $identities;

    /**
     * @param string $email
     */
    public function __construct($email = null)
    {
        $this->email = $email;
        $this->created = $this->updated = new \DateTime();
        $this->groups = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->identities = new ArrayCollection();
        $this->role = new Role();
        $this->is_admin = false;
        $this->is_public = false;
        $this->setPassword($this->generateRandomString(6)); // make sure password is not empty
        $this->points = 0;
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
     * Set username
     *
     * @param string $username
     * @return Newscoop\Entity\User
     */
    public function setUsername($username)
    {
        $username = preg_replace('~[^\\pL0-9_.]+~u', '-', $username);
        $username = trim($username, '-.');
        $this->username = str_replace('-', ' ', $username);

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
     * Get password salt for authentication (symfony)
     * @return string
     */
    public function getSalt()
    {
        list($algo, $salt, $password_hash) = explode(self::HASH_SEP, $this->password);
        return $salt;
    }

    /**
     * Get password for authentication (symfony)
     * @return [type] [description]
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        return true;
    }

    /**
     * Get random string
     *
     * @param int $length
     * @param string $allowed_chars
     * @return string
     */
    final public function generateRandomString($length = 12, $allowed_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getFirstName().' '.$this->getLastName();

        return $name;
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
     * Set user as active
     *
     * @return Newscoop\Entity\User
     */
    public function setActive()
    {
        return $this->setStatus(self::STATUS_ACTIVE);
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
     * Test if user is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status == self::STATUS_INACTIVE || empty($this->username);
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
     * Get created datetime
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated datetime
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
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
    public function setPublic($public = true)
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
     * Get points
     *
     * @return int
     */
    public function getPoints()
    {
        return (int) $this->points;
    }

    /**
     * Set points
     *
     * @param int $points
     * @return Newscoop\Entity\User
     */
    public function setPoints($points)
    {
        if (!is_int($points)) {
            throw new \InvalidArgumentException("Points must be an integer: '$points'");
        }

        $this->points = $points;

        return $this;
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
     * Get group names
     *
     * @return array
     */
    public function getGroupNames()
    {
        return $this->groups->map(function ($group) {
            return $group->getName();
        })->toArray();
    }

    /**
     * Test if user has group
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup($name)
    {
        foreach ($this->groups as $group) {
            if ($group->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add user type
     *
     * @param Newscoop\Entity\User\Group $type
     *
     * @return Newscoop\Entity\User
     */
    public function addUserType(Group $type)
    {
        $this->groups->add($type);

        return $this;
    }

    /**
     * Get user types
     *
     * @return array
     */
    public function getUserTypes()
    {
        return $this->getGroups();
    }

    /**
     * Get user roles for authentication (symfony)
     * @return array array with roles
     */
    public function getRoles()
    {
        $roles = array();
        foreach($this->groups as $group) {
            $roles[] = strtoupper(str_replace(" ", "_", $group->getName()));
        }

        return $roles;
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
     * Set User attributes
     * @param mixed $attributes UserAttributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get all user attributes
     *
     * @return array of all user attributes
     */
    public function getAttributes()
    {
        return array_filter($this->getRawAttributes());
    }

    /**
     * Get raw user attributes
     *
     * @return array
     */
    public function getRawAttributes()
    {
        $attributes = array();
        foreach ($this->attributes->getKeys() as $key) {
            $attributes[$key] = $this->attributes[$key]->getValue();
        }

        return $attributes;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Newscoop\Entity\User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Check permissions
     *
     * @param string $permission
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function hasPermission($permission, $resource = null, $action = null)
    {
        $blogService = \Zend_Registry::get('container')->getService('blog');
        if ($blogService->isBlogger($this)) {
            return true;
        }

        $acl = \Zend_Registry::get('acl')->getAcl($this);
        try {
            if (!$resource && !$action){
                list($resource, $action) = PermissionToAcl::translate($permission);
            }

            if($acl->isAllowed($this, strtolower($resource), strtolower($action))) {
                if (!$resource && !$action){
                    return \SaaS::singleton()->hasPermission($permission);
                }

                return true;
            } else {
                return FALSE;
            }
        } catch (\Exception $e) {
            return FALSE;
        }
    }

    public function getCommenters()
    {
       return $this->commenters;
    }


    /**
     * Get a User's comments which are associated with his User account.
     *
     * @return array
     */
    public function getComments()
    {
        $comments = array();

        foreach ($this->commenters as $commenter) {

            foreach ($commenter->getComments() as $comment) {
                $comments[] = $comment;
            }
        }

        return $comments;
    }

    /**
     * Get user id
     * proxy to getId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * Check if the user exists
     * Test if there is set an id
     *
     * @return bool
     * @deprecated legacy from frontend controllers
     */
    public function exists()
    {
        return !is_null($this->id);
    }

    /**
     * Get an enity property
     *
     * @param $p_key
     * @return mixed
     * @deprecated legacy from frontend controllers
     */
    public function getProperty($p_key)
    {
        if (method_exists($this, $p_key)) {
            return $this->$p_key();
        } else {
            throw new \InvalidArgumentException("User Property '$p_key' not found");
        }
    }

    /**
     * Set subscriber
     *
     * @param integer $subscriber
     * @return Newscoop\Entity\User
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get subscriber
     *
     * @return integer
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Set password reset token
     *
     * @param integer $resetToken
     * @return string
     */
    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Get password reset token
     *
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * Set author
     *
     * @param Newscoop\Entity\Author $author
     * @return Newscoop\Entity\User
     */
    public function setAuthor(Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author id
     *
     * @return int
     */
    public function getAuthorId()
    {
        return $this->author ? $this->author->getId() : null;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Set indexed
     *
     * @param DateTime $indexed
     *
     * @return self
     */
    public function setIndexed(DateTime $indexed = null)
    {
        $this->indexed = $indexed;

        return self;
    }

    /**
     * Get indexed
     *
     * @return DateTime
     */
    public function getIndexed()
    {
        return $this->indexed;
    }

    /**
     *
     * TODO: move this to user service - it's not a part of entity
     *
     * Update user profile
     *
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @param string $image
     * @param array $attributes
     */
    public function updateProfile($username, $password, $firstName, $lastName, $image, array $attributes)
    {
        if (!empty($username)) {
            $this->setUsername($username);
        }

        if (!empty($password)) {
            $this->setPassword($password);
        }

        if (!empty($firstName)) {
            $this->first_name = (string) $firstName;
        }

        if (!empty($lastName)) {
            $this->last_name = (string) $lastName;
        }

        if (!empty($image)) {
            $this->image = (string) $image;
        }

        foreach ($attributes as $key => $val) {
            if (isset($val)) {
                $this->addAttribute($key, $val);
            }
        }
    }

    /**
     * TODO: move this to user service - it's not a part of entity
     *
     * Get edit view
     *
     * @param Zend_View_Abstract $view
     * @return object
     */
    public function getEditView(Zend_View_Abstract $view)
    {
        return (object) array(
            'id' => $this->id,
            'username' => $this->username ?: sprintf('<%s>', preg_replace('/@.*$/', '', $this->email)),
            'email' => $this->email,
            'status' => $this->status,
            'created' => $this->created->format('d.m.Y H:i'),
            'updated' => $this->updated->format('d.m.Y H:i'),
            'is_verified' => (bool) $this->getAttribute(UserAttribute::IS_VERIFIED),
            'http_user_agent' => $this->getAttribute(UserAttribute::HTTP_USER_AGENT),
            'links' => array(
                array(
                    'rel' => 'edit',
                    'href' => $this->getViewUrl('edit', $view),
                ),
                array(
                    'rel' => 'delete',
                    'href' => $this->getViewUrl('delete', $view),
                ),
                array(
                    'rel' => 'token',
                    'href' => $this->getViewUrl('send-confirm-email', $view),
                ),
            ),
        );
    }

    /**
     * TODO: move this to user service - it's not a part of entity
     *
     * Get DataTable view
     *
     * @param Zend_View_Abstract $view
     * @return object
     */
    public function getDataTableView(Zend_View_Abstract $view)
    {
        $types = array();
        foreach ($this->getUserTypes() as $type) {
            $types[] = $type->getName();
        }

        switch ($this->status) {
            case '0':
                $status = 'Inactive';
                break;
            case '1':
                $status = 'Active';
                break;
            case '2':
                $status = 'Banned';
                break;
            case '3':
                $status = 'Deleted';
                break;
        }

        return (object) array(
            'id' => $this->id,
            'u' => $this->username ?: sprintf('<%s>', preg_replace('/@.*$/', '', $this->email)),
            'f' => $this->first_name,
            'l' => $this->last_name,
            'e' => $this->email,
            'g' => $types,
            's' => $status,
            'c' => $this->created->format('Y-m-d h:i:s'),
            'up' => $this->updated->format('Y-m-d h:i:s'),
            've' => ((bool) !$this->isPending() ? 'Yes' : 'No')
        );
    }

    /**
     * TODO: move this to user service - it's not a part of entity
     *
     * Get url for given action
     *
     * @param string $action
     * @param Zend_View_Abstract $view
     * @return string
     */
    private function getViewUrl($action, Zend_View_Abstract $view)
    {
        return $view->url(array(
            'module' => 'admin',
            'controller' => 'user',
            'action' => $action,
            'user' => $this->id,
        ), 'default', true);
    }

    /**
     * TODO: move this to user service - it's not a part of entity
     *
     * Rename user
     *
     * @param string $username
     * @return void
     */
    public function rename($username)
    {
        $this->setUsername($username);
    }

    /**
     * TODO: move this to user service - it's not a part of entity
     *
     * Render user
     *
     * @return UserView
     */
    public function render()
    {
        $view = new UserView();
        $view->username = $this->username;
        $view->email = $this->email;
        $view->first_name = $this->first_name;
        $view->last_name = $this->last_name;
        $view->identifier = $this->id;
        $view->uname = $view->username;
        $view->id = $this->id;

        $view->attributes = $this->getAttributes();
        foreach ($view->attributes as $key => $attribute) {
            if (!property_exists($view, $key)) {
                $view->$key = $attribute;
            }
        }

        return $view;
    }

    /**
     * Get view
     *
     * @return UserView
     */
    public function getView()
    {
        return $this->render();
    }

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($data)
    {
        $this->id = unserialize($data);
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->id === $user->getId();
    }

    /**
     * Set lastLogin
     *
     * @param DateTime $lastLogin
     * @return void
     */
    public function setLastLogin(\DateTime $lastLogin = null)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @param DateTime $lastLogin
     * @return void
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
}
