<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

/**
 * Ip entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\User\IpRepository")
 * @Table(name="SubsByIP")
 */
class Ip
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\User\Subscriber")
     * @JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User\Subscriber
     */
    private $subscriber;

    /**
     * @Id
     * @Column(type="integer", name="StartIP")
     * @var int
     */
    private $ip;

    /**
     * @Column(type="integer", name="Addresses")
     * @var int
     */
    private $number;

    /**
     * Set subscriber
     *
     * @param Newscoop\Entity\User\Subscriber $subscriber
     * @return Newscoop\Entity\User\Ip
     */
    public function setSubscriber(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
        return $this;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Newscoop\Entity\User\Ip
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
     * @return Newscoop\Entity\User\Ip
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
}

