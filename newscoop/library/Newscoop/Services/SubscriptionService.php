<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Subscription\Subscription;
use Newscoop\Subscription\SubscriptionData;

/**
 */
class SubscriptionService
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

    public function create(SubscriptionData $data)
    {
        $subscription = new Subscription();
        //apply data to subscription
        $subscription = $this->apply($subscription, $data);

        return $subscription;
    }

    public function remove($id) {

    }

    public function get($id)
    {
        
    }

    public function update($id, SubscriptionData $data)
    {

    }

    private function apply(Subscription $subscription, SubscriptionData $data) {
        if ($data->userId) {
            $user = $this->em->getRepository('Newscoop\Entity\User')->getOneActiveUser($data->userId, false)->getOneOrNullResult();
            if ($user) {
                $subscription->setUser($user);    
            }
        }

        if ($data->publicationId) {
            $publication = $this->em->getRepository('Newscoop\Entity\Publication')->findOneBy(array('id' => $data->publicationId));
            if ($publication) {
                $subscription->setPublication($publication);
            }
        }

        if ($data->toPay) {
            $subscription->setToPay($data->toPay);
        }

        if ($data->currency) {
            $subscription->setCurrency($data->currency);
        }

        return $subscription;
    }
}
