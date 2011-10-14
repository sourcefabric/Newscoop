<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\UserSubscription;

/**
 * User service
 */
class UserSubscriptionService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     *
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getSubscriptions($user)
    {
        return $this->getRepository()->findBy(array('user' => $user->getId()));
    }
    
    public function createKey($user)
    {
        $key = md5($user->getId().$user->getEmail().time());
        return($key);
    }
    
    public function setKey($key)
    {
        // send request
    }

    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\UserSubscription');
    }
}
