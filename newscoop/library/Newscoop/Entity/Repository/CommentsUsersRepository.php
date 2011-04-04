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
 * Comments users repository
 */
class CommentsUsersRepository extends EntityRepository
{
    /**
     * Get comments users list
     *
     * @param int $p_offset
     * @param int $p_limit
     * @param string|NULL $p_name
     * @return array
     */
    public function getList($p_offset, $p_limit, $p_name = NULL)
    {
        $qb = $this->createQueryBuilder('cu');

        $qb = $this->filterByName($qb, $p_name);

        return $qb->orderBy('cu.time_created', 'DESC')
            ->setFirstResult((int) $p_offset)
            ->setMaxResults((int) $p_limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get comments users count
     *
     * @param string|NULL $p_name
     * @return int
     */
    public function getCount($p_name = NULL)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(cu)')
            ->from('Newscoop\Entity\CommentsUsers', 'cu');

        $qb = $this->filterByName($qb, $p_name);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Filter query by name
     *
     * @param Doctrine\ORM\QueryBuilder $p_qb
     * @param string $name
     * @return Doctrine\ORM\QueryBuilder
     */
    private function filterByName(QueryBuilder $p_qb, $p_name)
    {
        if ($p_name != '') {
            $p_qb->where('cu.name = :name')
                ->setParameter('name', (string) $name);
        }

        return $p_qb;
    }
}
