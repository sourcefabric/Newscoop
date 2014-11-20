<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Newscoop\CommunityTickerBundle\Entity\Repository\CommunityTickerEventRepository")
 * @ORM\Table(name="community_ticker_event")
 */
class CommunityTickerEvent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @var string
     */
    private $event;

    /**
     * @ORM\Column(type="text", nullable=True)
     * @var string
     */
    private $params;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @var boolean
     */
    private $is_active;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->setIsActive(true);
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
     * Set event
     *
     * @param string $event
     *
     * @return CommunityTickerEvent
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set params
     *
     * @param array $params
     *
     * @return CommunityTickerEvent
     */
    public function setParams(array $params)
    {
        $this->params = json_encode($params);

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return !empty($this->params) ? json_decode($this->params, true) : array();
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     *
     * @return CommunityTickerEvent
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set status
     *
     * @param boolean $is_active
     *
     * @return CommunityTickerEvent
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User $user
     *
     * @return CommunityTickerEvent
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return null|User
     */
    public function getUser()
    {
        return $this->user;
    }
}
