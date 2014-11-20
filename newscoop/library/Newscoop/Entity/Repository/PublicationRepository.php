<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

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
}
