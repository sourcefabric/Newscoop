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
class CommentsRepository extends EntityRepository
{
    private $status_mapper = array(
        'approved' => 1,
        'pending' => 2,
        'hidden'  => 3,
        'deleted' => 4
    );

    /**
     * Get comments users list
     *
     * @param int $p_offset
     * @param int $p_limit
     * @param string|NULL $p_name
     * @return array
     */
    public function getList($p_params = array())
    {

        $p_params = array_merge(array(
            'offset' => 0,
            'limit' => 1,
            'order' => array('by' => 'time_created', 'dir' => 'desc'),
            'search' => '',
            'colums' => array('status' => 'approved')
        ), is_array($p_params)? $p_params: array('search' => $p_params));

        $colums_keys = array_keys($p_params['colums']);
        $colums_values = array_values($p_params['colums']);
        $qb = $this->createQueryBuilder('c');

        $qb = $this->filterBySearch($qb, $p_params['search']);
        /*$column_value = isset($this->status_mapper[$colums_values[0]])?
            $this->status_mapper[$colums_values[0]] :
            $colums_values[0];
        */
        $qb->orderBy('c.'.$p_params['order']['by'], $p_params['order']['dir'])
            ->andWhere('c.'.$colums_keys[0].' = :column_value')
            ->setParameter('column_value', $this->status_mapper[$colums_values[0]])
            ->setFirstResult((int) $p_params['offset'])
            ->setMaxResults((int) $p_params['limit']);
        return $qb->getQuery()->getResult();
    }

    /**
     * Get comments users count
     *
     * @param array|NULL $p_params
     * @return int
     */
    public function getCount($p_params = array())
    {
        $p_params = array_merge(array(
            'search'=>'',
            'colums'=>array('status'=>'approved')
        ), is_array($p_params)? $p_params: array('search' => $p_params));
        print_r($p_params);
        $colums_keys = array_keys($p_params['colums']);
        $colums_values = array_values($p_params['colums']);
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(c)')
            ->from('Newscoop\Entity\Comments', 'c')
            ->andWhere('c.'.$colums_keys[0].' = :column_value')
            ->setParameter('column_value', $this->status_mapper[$colums_values[0]]);
        print_r($this->status_mapper[$colums_values[0]]);
        $qb = $this->filterBySearch($qb, $p_search);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Filter query by name
     *
     * @param Doctrine\ORM\QueryBuilder $p_qb
     * @param string $name
     * @return Doctrine\ORM\QueryBuilder
     */
    private function filterBySearch(QueryBuilder $p_qb, $p_search)
    {
        if ($p_name != '') {
            $p_qb->where('c.name = :name')
            ->setParameter('name', (string) $p_search);
        }

        return $p_qb;
    }
}
