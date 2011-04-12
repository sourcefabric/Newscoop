<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Entity\User;

/**
 * Subscriber entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\User\SubscriberRepository")
 */
class Subscriber extends User
{
    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Subscription", mappedBy="subscriber")
     * @var array
     */
    private $subscriptions;

    /**
     */
    public function __construct()
    {
        parent::__construct();

        $this->reader = 'Y';
        $this->subscriptions = new ArrayCollection;
    }

    /**
     * Get subscriptions
     *
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * Has subscriptions?
     *
     * @return bool
     */
    public function hasSubscriptions()
    {
        return !$this->subscriptions->isEmpty();
    }
}

