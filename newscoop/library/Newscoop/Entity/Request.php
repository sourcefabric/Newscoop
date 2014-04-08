<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Newscoop\Entity\Session;

/**
 * Session entity
 *
 * @ORM\Entity
 * @ORM\Table(name="Requests")
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="requests")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    protected $session;

    /**
     * @ORM\Column(type="datetime", name="last_stats_update")
     * @var string
     */
    protected $last_stats_update;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="object_id", length=11)
     * @var string
     */
    protected $object_id;

    /**
     * Get $session_id
     * @return string
     */
    public function getSessionId() {
        return $this->session_id;
    }

    /**
     * Set $session
     * @param Session $session
     */
    public function setSession(Session $session) {
        $this->session = $session;

        return $this;
    }

    /**
     * Get $last_stats_update
     * @return \DateTime
     */
    public function getLastStatsUpdate() {
        return $this->last_stats_update;
    }

    /**
     * Set $last_stats_update
     * @param \DateTime $lastStatsUpdate
     */
    public function setLastStatsUpdate(\DateTime $lastStatsUpdate) {
        $this->last_stats_update = $lastStatsUpdate;

        return $this;
    }

    /**
     * Get $object_id
     * @return integer
     */
    public function getObjectId() {
        return $this->object_id;
    }

    /**
     * Set $object_id
     * @param integer $objectId
     */
    public function setObjectId($objectId) {
        $this->object_id = $objectId;

        return $this;
    }
}
