<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Session entity
 *
 * @ORM\Entity
 * @ORM\Table(name="Sessions")
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="start_time")
     */
    protected $start_time;

    /**
     * @ORM\Column(type="integer", name="user_id", nullable=true)
     */
    protected $user_id;

    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="session_id")
     */
    protected $requests;

    /**
     * Construct Session object
     */
    public function __construct() {
        $this->requests = new ArrayCollection();
    }

    /**
     * Get $id
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get $start_time
     * @return \DateTime $start_time
     */
    public function getStartTime() {
        return $this->start_time;
    }

    /**
     * Set $start_time
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime) {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get $user_id
     * @return integer $user_id
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * Set $user_id
     * @param integer $userId
     */
    public function setUserId($userId) {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get $requests
     * @return ArrayCollection Session requests
     */
    public function getRequests() {
        return $this->requests;
    }

    /**
     * Add new request to Session
     * @param \Newscoop\Entity\Request $request 
     */
    public function addRequest(\Newscoop\Entity\Request $request) {
        $this->requests[] = $request;

        return $this;
    }
}
