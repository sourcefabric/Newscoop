<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use DateTime, Newscoop\Entity\User;

/**
 * Feedback entity
 * @entity
 * @table(name="feedback")
 * @entity(repositoryClass="Newscoop\Entity\Repository\FeedbackRepository")
 */
class Feedback
{
	/**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
	private $id;
    
    /**
     * @manyToOne(targetEntity="Newscoop\Entity\User")
     * @joinColumn(name="user_id", referencedColumnName="id")
     * @var Newscoop\Entity\User
     */
    private $user;
    
    /**
     * @column(length=2048)
     * @var text
     */
    private $message;
    
    /**
     * @column(length=128)
     * @var string
     */
    private $url;
    
    /**
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_created;

    /*
     * @column(type="datetime")
     * @var DateTime
     */
    private $time_updated;
    
    /**
     * Set id
     *
     * @param int $p_id
     * @return Newscoop\Entity\User
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set time created
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Feedback
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
     * Set time updated
     *
     * @param DateTime $p_datetime
     * @return Newscoop\Entity\Feedback
     */
    public function setTimeUpdated(DateTime $p_datetime)
    {
        $this->time_updated = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get update time.
     *
     * @return DateTime
     */
    public function getTimeUpdated()
    {
        return $this->time_updated;
    }
    
    /**
     * Set url.
     *
     * @param string $p_url
     * @return Newscoop\Entity\Feedback
     */
    public function setUrl($p_url)
    {
        $this->url = (string)$p_url;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get comment url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set message.
     *
     * @param string $p_message
     * @return Newscoop\Entity\Feedback
     */
    public function setMessage($p_message)
    {
        $this->message = $p_message;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Set user
     *
     * @param Newscoop\Entity\User $p_user
     * @return Newscoop\Entity\Feedback
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
}