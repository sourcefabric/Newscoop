<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User;

/**
 * User Topic Repository
 */
class UserTopicRepository extends EntityRepository
{
    /**
     * Find topics for user
     *
     * @param Newscoop\Entity\User
     * @return array
     */
    public function findByUser($user)
    {
        $userId = is_int($user) ? $user : $user->getId();
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT ut FROM Newscoop\Entity\UserTopic ut INNER JOIN ut.topic t WHERE ut.user = :user');
        $query->setParameter('user', $userId);
        return $query->getResult();
    }
}
