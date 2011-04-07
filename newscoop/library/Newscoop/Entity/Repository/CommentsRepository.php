<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Entity\Comments;

/**
 * Comments users repository
 */
class CommentsRepository extends EntityRepository
{

    /**
     * Get status code from the string provided
     *
     * @param string $p_status
     * @return int
     */
    public function getStatusCode($p_status)
    {
        return $this->status_mapper[$p_status];
    }

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
            ->setParameter('column_value', $this->getStatusCode($colums_values[0]))
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
    public function getCount_old($p_params = array())
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
            ->from('Comments', 'c')
            ->andWhere('c.'.$colums_keys[0].' = :column_value')
            ->setParameter('column_value', $this->getStatusCodesta($colums_values[0]));
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
    /**
     * Get data for table
     *
     * @param array $params
     * @param array $cols
     * @return array
     */
    public function getData(array $params, array $cols)
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($params['sSearch'])) {
            $qb->where($this->buildWhere($cols, $params['sSearch']));
        }

        // sort
        foreach (array_keys($cols) as $id => $property) {
            if (!is_string($property)) { // not sortable
                continue;
            }

            if (isset($params["iSortCol_$id"])) {
                $dir = $params["sSortDir_$id"] ?: 'asc';
                $qb->orderBy("e.$property", $dir);
            }
        }

        // limit
        $qb->setFirstResult((int) $params['iDisplayStart'])
            ->setMaxResults((int) $params['iDisplayLength']);


        return $qb->getQuery()->getResult();
    }

    /**
     * Get user count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get filtered count
     *
     * @param array $params
     * @param array $cols
     * @return int
     */
    public function getFilteredCount(array $params, array $cols)
    {
        if (empty($params['sSearch'])) {
            return $this->getCount();
        }

        return $this
            ->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->where($this->buildWhere($cols, $params['sSearch']))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    private function buildWhere(array $cols, $search)
    {
        $qb = $this->createQueryBuilder('e');
        $or = $qb->expr()->orx();
        foreach (array_keys($cols) as $i => $property) {
            if (!is_string($property)) { // not searchable
                continue;
            }

            $or->add($qb->expr()->like("e.$property", $qb->expr()->literal("%{$search}%")));
        }

        return $or;
    }

}
