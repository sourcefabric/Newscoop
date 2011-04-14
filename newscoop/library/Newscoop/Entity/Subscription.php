<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Entity\Publication,
    Newscoop\Entity\User\Subscriber;

/**
 * Subscription entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\SubscriptionRepository")
 * @Table(name="Subscriptions")
 */
class Subscription
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\User\Subscriber")
     * @JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User\Subscriber
     */
    private $subscriber;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @Column(type="decimal", name="ToPay")
     * @var float
     */
    private $toPay = 0.0;

    /**
     * @Column(name="Type")
     * @var string
     */
    private $type;

    /**
     * @Column(name="Active")
     * @var string
     */
    private $active;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\SubscriptionSection", mappedBy="subscription")
     * @var array
     */
    private $sections;

    /**
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set subscriber
     *
     * @param Newscoop\Entity\User\Subscriber $subscriber
     * @return Newscoop\Entity\Subscription
     */
    public function setSubscriber(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
        return $this;
    }

    /**
     * Set publication
     *
     * @param Newscoop\Entity\Publication $publication
     * @return Newscoop\Entity\Subscription
     */
    public function setPublication(Publication $publication)
    {
        $this->publication = $publication;
        return $this;
    }

    /**
     * Get publication
     *
     * @return Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Get publication name
     *
     * @return string
     */
    public function getPublicationName()
    {
        return $this->publication->getName();
    }

    /**
     * Set to pay
     *
     * @param float $toPay
     * @return Newscoop\Entity\Subscription
     */
    public function setToPay($toPay)
    {
        $this->toPay = (float) $toPay;
        return $this;
    }

    /**
     * Get to pay
     *
     * @return float
     */
    public function getToPay()
    {
        return (float) $this->toPay;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Newscoop\Entity\Subscription
     */
    public function setType($type)
    {
        $this->type = strtolower($type) == 't' ? 'T' : 'P';
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set active
     *
     * @param bool $active
     * @return Newscoop\Entity\Subscription
     */
    public function setActive($active)
    {
        $this->active = (bool) $active ? 'Y' : 'N';
        return $this;
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        return strtolower($this->active) == 'y';
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }
}

