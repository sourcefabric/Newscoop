<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Subscription,
    Newscoop\Entity\User\Subscriber;

/**
 * Subscription repository
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * Save subscription
     *
     * @param Newscoop\Entity\User\Subscription $subscription
     * @param Newscoop\Entity\User\Subscriber $user
     * @param array $values
     * @return void
     */
    public function save(Subscription $subscription, Subscriber $user, array $values)
    {
        $em = $this->getEntityManager();

        $subscription->setPublication($em->getReference('Newscoop\Entity\Publication', (int) $values['publication']))
            ->setActive((int) $values['active'])
            ->setSubscriber($user)
            ->setType($values['type']);

        $em->persist($subscription);

        if (strtolower($values['sections']) == 'y') { // add sections
            // @todo
        }
    }
}
