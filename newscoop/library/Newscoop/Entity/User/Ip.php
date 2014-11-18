<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ip entity
 * @ORM\Entity
 * @ORM\Table(name="SubsByIP")
 */
class Ip
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="StartIP")
     * @var int
     */
    protected $ip;

    /**
     * @ORM\Column(type="integer", name="Addresses")
     * @var int
     */
    protected $number;

    /**
     * @param string $ip
     * @param int $number
     */
    public function __construct($ip, $number)
    {
        $this->setIp($ip);
        $this->setNumber($number);
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User $user
     * @return Ip
     */
    public function setUser(\Newscoop\Entity\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Ip
     */
    public function setIp($ip)
    {
        $this->ip = ip2long($ip);
        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return long2ip($this->ip);
    }

    /**
     * Set number
     *
     * @param int $number
     * @return Ip
     */
    public function setNumber($number)
    {
        $this->number = (int) $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return (int) $this->number;
    }

    /**
     * To string strategy
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getIp();
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user !== null ? $this->user->getId() : null;
    }
}
