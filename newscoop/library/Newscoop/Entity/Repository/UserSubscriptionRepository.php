<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity,
Doctrine\ORM\EntityRepository,
Newscoop\Entity\UserSubscription,
Newscoop\Entity\User;

class UserSubscriptionRepository extends EntityRepository
{

    /**
     * Get new instance of user_subscription
     */
    public function getPrototype()
    {
        return new UserSubscription;
    }

    /**
     * Add to notification
     *
     * @return unknown_type
     */
    public function save(UserSubscription $entity, $values)
    {
        $em = $this->getEntityManager();
        
        if (array_key_exists('user', $values)) {
            $entity->setUser($values['user']);
        }
        if (array_key_exists('subscription_type', $values)) {
            $entity->setSubscriptionType($values['subscription_type']);
        }
        if (array_key_exists('time_begin', $values)) {
            $entity->setTimeBegin(new \DateTime($values['time_begin']));
        }
        if (array_key_exists('time_end', $values)) {
            $entity->setTimeEnd(new \DateTime($values['time_end']));
        }
        if (array_key_exists('subscription', $values)) {
            $entity->setSubscription($values['subscription']);
        }
        
        $em->persist($entity);
        return $entity;
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}