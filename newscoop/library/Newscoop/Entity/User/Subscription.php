<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

/**
 * Subscription entity
 * @Entity
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
    private $toPay;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
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
     * Get to pay
     *
     * @return float
     */
    public function getToPay()
    {
        return (float) $this->toPay;
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
     * @return Newscoop\Entity\User\Subscription
     */
    public function setActive($active)
    {
        $this->active = (bool) $active ? 'Y' : 'N';
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
}

