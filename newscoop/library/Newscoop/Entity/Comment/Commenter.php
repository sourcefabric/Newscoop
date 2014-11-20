<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Comment;

use DateTime;
use Newscoop\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commenter entity
 * @ORM\Table(name="comment_commenter")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\Comment\CommenterRepository")
 */
class Commenter
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User", inversedBy="commenters")
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Column(length=100, nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(length=100, nullable=false)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(length=255, nullable=true)
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(length=39, nullable=true)
     * @var int
     */
    protected $ip;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    protected $time_created;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var DateTime
     */
    private $time_updated;

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Comment", mappedBy="commenter", cascade={"persist", "remove"})
     */
    protected $comments;

    public function __construct()
    {
        $this->setUrl('');
        $this->setTimeCreated(new \DateTime());
        $this->setTimeUpdated(new \DateTime());
    }

    /**
     * Set commenter id
     *
     * @param int $id
     *
     * @return Commenter
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get user id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set commenter full name
     *
     * @param string $name
     *
     * @return Commenter
     */
    public function setName($name)
    {
        $this->name = (string) $name;

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
     * @param string $email
     *
     * @return Commenter
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;

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
     *
     * @return Commenter
     */
    public function setUser(User $user)
    {
        $this->user = $user;

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
     * @param string $url
     *
     * @return Commenter
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;

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
     * @param string $ip
     *
     * @return Commenter
     */
    public function setIp($ip)
    {
        // remove subnet & limit to IP_LENGTH
        $ip_array = explode('/', (string) $ip);
        $this->ip = substr($ip_array[0], 0, 39);

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
     * @param DateTime $datetime
     *
     * @return Commenter
     */
    public function setTimeCreated(DateTime $datetime)
    {
        $this->time_created = $datetime;

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
     * Set time updated
     *
     * @param DateTime $datetime
     *
     * @return Commenter
     */
    public function setTimeUpdated(DateTime $datetime)
    {
        $this->time_updated = $datetime;
        
        return $this;
    }

    /**
     * Get updated time.
     *
     * @return DateTime
     */
    public function getTimeUpdated()
    {
        return $this->time_updated;
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
