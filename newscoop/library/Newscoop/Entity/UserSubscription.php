<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Newscoop\Entity\User;

/**
 * UserSubscription entity
 * @entity
 * @table(name="user_subscription")
 * @entity(repositoryClass="Newscoop\Entity\Repository\UserSubscriptionRepository")
 */
class UserSubscription
{
    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;
    
    /**
     * @manyToOne(targetEntity="Newscoop\Entity\User")
     * @joinColumn(name="user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;
    
    /**
     * @column(length=2)
     * @var int
     */
    private $subscription_type;
    
    /**
     * @var string to code mapper for status
     */
    static $subscription_type_enum = array('standard', 'student');
    
    /*
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_begin;
    
    /*
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_end;
    
    /**
     * Set id
     *
     * @param int $p_id
     * @return Newscoop\Entity\UserSubscription
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
        return $this;
    }

    /**
     * Get id
     *
     * @return Newscoop\Entity\UserSubscription
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set user
     *
     * @param Newscoop\Entity\User $p_user
     * @return Newscoop\Entity\UserSubscription
     */
    public function setUser(User $p_user)
    {
        $this->user = $p_user;
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
     * Set status
     *
     * @return Newscoop\Entity\UserSubscription
     */
    public function setSubscriptionType($p_subscription_type)
    {
        $subscription_type_enum = array_flip(self::$subscription_type_enum);
        $this->status = $status_enum[$p_subscription_type];
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getSubscriptionType()
    {
        return self::$subscription_type_enum[$this->subscription_type];
    }
    
    /**
     * Set timebegin
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\UserSubscription
     */
    public function setTimeBegin(\DateTime $p_datetime)
    {
        $this->time_begin = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get timebegin.
     *
     * @return DateTime
     */
    public function getTimeBegin()
    {
        return $this->time_begin;
    }
    
    /**
     * Set timeend
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\UserSubscription
     */
    public function setTimeEnd(\DateTime $p_datetime)
    {
        $this->time_end = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get timeend.
     *
     * @return DateTime
     */
    public function getTimeEnd()
    {
        return $this->time_end;
    }
}
