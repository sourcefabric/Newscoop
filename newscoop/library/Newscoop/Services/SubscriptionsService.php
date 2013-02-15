<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Subscription\SubscriptionData;

/**
 */
class SubscriptionsService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createNewSubscription(SubscriptionData $data)
    {
        //apply data to subscription
        //add sections to subscription
        //add articles to subscription
    }

    public function removeSubscription($id) {

    }

    public function getSubscription($id)
    {
        
    }

    public function extendSubscription($id, $newDate)
    {

    }
}
