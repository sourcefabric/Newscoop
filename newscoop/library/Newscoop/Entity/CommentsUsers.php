<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use DateTime,
    Newscoop\Entity\User;

/**
 * User entity
 * @entity
 * @table(name="CommentsUsers")
 * @entity(repositoryClass="Newscoop\Entity\Repository\CommentsUsersRepository")
 */
class CommentsUsers
{

    const IP_LENGTH = 39; // IPv6 ready

    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @manyToOne(targetEntity="User")
     * @joinColumn(name="fk_user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @column(length=100)
     * @var string
     */
    private $name;

    /**
     * @column(length=100)
     * @var string
     */
    private $email;

    /**
     * @column(length=255)
     * @var string
     */
    private $url;

    /**
     * @column(length=39)
     * @var int
     */
    private $ip;

    /**
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_created;


    /**
     * Set comment user id
     *
     * @param int $p_id
     * @return Newscoop\Entity\CommentsUsers
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
     * Set comment user full name
     *
     * @param string $p_name
     * @return Newscoop\Entity\CommentsUsers
     */
    public function setName($p_name)
    {
        $this->name = (string) $p_name;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set comment user email address
     *
     * @param string $p_email
     * @return Newscoop\Entity\CommentsUsers
     */
    public function setEmail($p_email)
    {
        $this->email = (string) $p_email;
        // return this for chaining mechanism
        return $this;
    }

   /**
     * Get user email address
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
     * @return Newscoop\Entity\CommentsUsers
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
     * @return Newscoop\Entity\CommentsUsers
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
     * @return Newscoop\Entity\Log
     */
    public function setIp($p_ip)
    {
        // remove subnet & limit to IP_LENGTH
        $ip_array = explode('/', (string) $p_ip);
        $this->ip = substr($ip_array[0], 0, self::IP_LENGTH);
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
     * @return Newscoop\Entity\Log
     */
    public function setTimeCreated(DateTime $p_datetime)
    {
        $this->time_created = $p_datetime;
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

}