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
    public function getPublications()
    {
        $em = $this->getEntityManager();
        $qb = $em->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p')
            ->select('p', 'a', 'l')
            ->leftJoin('p.defaultAlias', 'a')
            ->leftJoin('p.language', 'l');

        return $qb->getQuery();
    }
}
