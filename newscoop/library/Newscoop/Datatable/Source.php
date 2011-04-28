<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Datatable;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder;

/**
 * Class implementation of the IDatatableSource class
 *
 */
class Source extends EntityRepository implements ISource
{
    /**
     * Get data for table
     *
     * @param array $p_params
     * @param array $p_cols
     * @return array
     */
    public function getData(array $p_params, array $p_cols)
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($p_params['sSearch'])) {
            $qb->where($this->buildWhere($p_cols, $p_params['sSearch']));
        }

        // sort
        foreach (array_keys($p_cols) as $id => $property) {
            if (!is_string($property)) { // not sortable
                continue;
            }

            if (isset($p_params["iSortCol_$id"])) {
                $dir = $p_params["sSortDir_$id"] ?: 'asc';
                $qb->orderBy("e.$property", $dir);
            }
        }

        // limit
        $qb->setFirstResult((int) $p_params['iDisplayStart'])
            ->setMaxResults((int) $p_params['iDisplayLength']);


        return $qb->getQuery()->getResult();
    }

    /**
     * Get entity count
     *
     * @param array $p_params|null
     * @param array $p_cols|null
     *
     * @return int
     */
    public function getCount(array $p_params = null, array $p_cols = null)
    {

        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e)');
        if(is_array($p_params) && !empty($p_params['sSearch'])) {
            $qb->where($this->buildWhere($p_cols, $p_params['sSearch']));
        }
        return $qb->getQuery()->getSingleScalarResult();
    }


    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    private function buildWhere(array $p_cols, $p_search)
    {
        $qb = $this->createQueryBuilder('e');
        $or = $qb->expr()->orx();
        foreach (array_keys($p_cols) as $i => $property) {
            if (!is_string($property)) { // not searchable
                continue;
            }

            $or->add($qb->expr()->like("e.$property", $qb->expr()->literal("%{$p_search}%")));
        }

        return $or;
    }

}