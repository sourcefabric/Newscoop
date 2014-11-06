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
    public function getPubications()
    {
        $em = $this->getEntityManager();
        $qb = $em->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p');

        return $qb->getQuery();
    }
}
