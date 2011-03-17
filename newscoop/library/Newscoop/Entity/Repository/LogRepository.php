<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder;

/**
 * Log repository
 */
class LogRepository extends EntityRepository
{
    /**
     * Get logs
     *
     * @param int $offset
     * @param int $limit
     * @param int|NULL $priority
     * @return array
     */
    public function getLogs($offset, $limit, $priority = NULL)
    {
        $qb = $this->createQueryBuilder('l');

        $this->filterByPriority($qb, $priority);

        return $qb->orderBy('l.time_created', 'DESC')
            ->setFirstResult((int) $offset)
            ->setMaxResults((int) $limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get logs count
     *
     * @param int|NULL $priority
     * @return int
     */
    public function getCount($priority = NULL)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(l)')
            ->from('Newscoop\Entity\Log', 'l');

        $this->filterByPriority($qb, $priority);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Filter query by priority
     *
     * @param Doctrine\ORM\QueryBuilder $qb
     * @param int $priority
     * @return Doctrine\ORM\QueryBuilder
     */
    private function filterByPriority(QueryBuilder $qb, $priority)
    {
        if (isset($priority)) {
            $qb->where('l.priority = :priority')
                ->setParameter('priority', (int) $priority);
        }

        return $qb;
    }
}
