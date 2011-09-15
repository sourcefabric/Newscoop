<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use DateTime,
    InvalidArgumentException,
    Zend_Log,
    Newscoop\Entity\User;

/**
 * Log entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\LogRepository")
 */
class Log
{
    const PRIORITY_DEFAULT = Zend_Log::INFO;

    const PRIORITY_MAX = 255; // 1 byte

    const IP_LENGTH = 39; // IPv6 ready

    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_created;

    /**
     * @column(length=255)
     * @var string
     */
    private $text;

    /**
     * @column(length=39)
     * @var int
     */
    private $user_ip;

    /**
     * @column(type="smallint")
     * @var int
     */
    private $priority;

    /**
     * @manyToOne(targetEntity="User")
     * @joinColumn(name="fk_user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * Set time created
     *
     * @param DateTime $datetime
     * @return Newscoop\Entity\Log
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
     * Set text
     *
     * @param string $text
     * @return Newscoop\Entity\Log
     */
    public function setText($text)
    {
        $this->text = (string) $text;
        return $this;
    }

    /**
     * Get log text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set client ip
     *
     * @param string $ip
     * @return Newscoop\Entity\Log
     */
    public function setClientIP($ip)
    {
        // remove subnet & limit to IP_LENGTH
        $ip_ary = explode('/', (string) $ip);
        $this->user_ip = substr($ip_ary[0], 0, self::IP_LENGTH);
        return $this;
    }


    /**
     * Get client ip
     *
     * @return string
     */
    public function getClientIP()
    {
        if (is_numeric($this->user_ip)) { // try to use old format
            static $max = 0xffffffff; // 2^32
            if ($this->user_ip > 0 && $this->user_ip < $max) {
                return long2ip($this->user_ip);
            }
        }

        return (string) $this->user_ip;
    }

    /**
     * Set priority
     *
     * @param int $priority
     * @return Newscoop\Entity\Log
     */
    public function setPriority($priority)
    {
        $this->priority = min(self::PRIORITY_MAX, max(0, (int) $priority));
        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     * @return Newscoop\Entity\Log
     */
    public function getPriority()
    {
        if (!isset($this->priority)) {
            return self::PRIORITY_DEFAULT;
        }

        return $this->priority;
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User|NULL $user
     * @return Newscoop\Entity\Log
     */
    public function setUser(User $user = NULL)
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
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->getUser() ? $this->getUser()->getName() : '';
    }
}

