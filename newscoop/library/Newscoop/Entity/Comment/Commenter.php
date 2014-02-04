<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Comment;

use DateTime;
use Newscoop\Entity\User;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Commenter entity
 * @ORM\Table(name="comment_commenter")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\Comment\CommenterRepository")
 */
class Commenter
{

    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User", inversedBy="commenters")
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(length=100)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(length=100)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(length=255)
     * @var string
     */
    private $url;

    /**
     * @ORM\Column(length=39)
     * @var int
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $time_created;

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Comment", mappedBy="commenter", cascade={"persist", "remove"})
     */
    private $comments;

    public function __construct()
    {
        $this->setUrl('');
        $this->setTimeCreated(new \DateTime());
    }

    /**
     * Set commenter id
     *
     * @param int $p_id
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get user id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set commenter full name
     *
     * @param string $p_name
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setName($p_name)
    {
        $this->name = (string) $p_name;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get commenter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set commenter email address
     *
     * @param string $p_email
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setEmail($p_email)
    {
        $this->email = (string) $p_email;
        // return this for chaining mechanism
        return $this;
    }

   /**
     * Get commenter email address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User $user
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        // return this for chaining mechanism
        return $this;
    }
    /**
     * Get user
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set comment user url
     *
     * @param string $p_url
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setUrl($p_url)
    {
        $this->url = (string) $p_url;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get comment user url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set ip
     *
     * @param string $p_ip
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setIp($p_ip)
    {
        // remove subnet & limit to IP_LENGTH
        $ip_array = explode('/', (string) $p_ip);
        $this->ip = substr($ip_array[0], 0, 39);
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get client ip
     *
     * @return string
     */
    public function getIp()
    {
        if (is_numeric($this->ip)) { // try to use old format
            static $max = 0xffffffff; // 2^32
            if ($this->ip > 0 && $this->ip < $max) {
                return long2ip($this->ip);
            }
        }

        return (string) $this->ip;
    }

    /**
     * Set time created
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Comment\Commenter
     */
    public function setTimeCreated(DateTime $p_datetime)
    {
        $this->time_created = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * Get name of the linked user if there is one
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->getUser() ? $this->getUser()->getName() : '';
    }

    /**
     * Get id of the linked user if there is one
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->getUser() ? $this->getUser()->getId() : '';
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function __toString() {
        return $this->name;
    }
}
