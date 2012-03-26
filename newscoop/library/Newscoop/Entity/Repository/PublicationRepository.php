<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User\Subscriber;

/**
 * Publication repository
 */
class PublicationRepository extends EntityRepository
{
    /**
     * Get subscriber options
     *
     * @param Newscoop\Entity\User $user
     * @return array
     */
    public function getSubscriberOptions(User $user)
    {
        $em = $this->getEntityManager();

        $query = $this->createQueryBuilder('p')
            ->andWhere('p.id NOT IN (:subscribed)')
            ->getQuery();

        $query->setParameter('subscribed', $this->getSubscribedPublications($user));

        $publications = array();
        foreach ($query->getResult() as $publication) {
            $publications[$publication->getId()] = $publication->getName();
        }

        return $publications;
    }
    /**

        // get publications subscribed already
        $query = $em->createQuery('SELECT p.id FROM Newscoop\Subscription s JOIN s.subscriber u JOIN s.publication p WHERE u.id = ?1');
        $query->setParameter(1, $user->getId());
        $rows = $query->getResult();
        $ids = array_reduce($rows, function($next, $current) {
            $next += array_values($current);
            return $next;
        }, array());

        // get not subscribed publications
        $qb = $this->createQueryBuilder('p');

        if (!empty($ids)) {
            $qb->where($em->getExpressionBuilder()->notIn('p.id', $ids));
        }

        $query = $qb->getQuery();
        $rows = $query->getResult();

        // format options
        // */
}
