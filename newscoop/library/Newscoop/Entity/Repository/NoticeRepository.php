<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity\Notice;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Datatable\Source as DatatableSource;


/**
 * Notice repository.
 */
class NoticeRepository extends DatatableSource
{
    /**
     * Get new instance of the comment
     *
     * @return \Newscoop\Entity\Comment
     */
    public function getPrototype()
    {
        return new Notice;
    }

    /**
     * Get data for table
     *
     * @param array $p_params
     * @param array $cols
     * @return Comment[]
     */
    public function getData(array $p_params, array $p_cols)
    {
        $qb = $this->createQueryBuilder('n');
        $andx = $qb->expr()->andx();

        if (!empty($p_params['sSearch'])) {
            $orx = $this->buildWhere($p_cols, $p_params['sSearch'], $qb);
            $andx->add($orx);
        }


        if (!empty($p_params['sFilter'])) {
            $andx = $this->buildFilter($p_cols, $p_params['sFilter'], $qb, $andx);
        }

        if($andx->count()){
            $qb->where($andx);
        }

        // sort
        if (isset($p_params["iSortCol_0"])) {
            $cols = array_keys($p_cols);
            $sortId = $p_params["iSortCol_0"];
            $sortBy = $cols[$sortId];
            $dir = $p_params["sSortDir_0"] ? : 'asc';
            switch ($sortBy) {
                case 'lastname':
                    $qb->orderBy("n.lastname", $dir);
                    break;
                case 'published':
                    $qb->orderBy("n.published", $dir);
                    break;
                case 'id':
                    $qb->orderBy("n.id", $dir);
                    break;
                default:
                    $qb->orderBy("n." . $sortBy, $dir);
            }
        }


        // limit
        if (isset($p_params['iDisplayLength'])) {
            $qb->setFirstResult((int)$p_params['iDisplayStart'])->setMaxResults((int)$p_params['iDisplayLength']);
        }

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
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n)');
        if (is_array($p_params) && !empty($p_params['sSearch'])) {
            $qb->where($this->buildWhere($p_cols, $p_params['sSearch'], $qb));
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Build filter condition
     *
     * @param array $p_
     * @param string $p_cols
     * @param
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildFilter(array $p_cols, array $p_filter, $qb, $andx)
    {
        foreach ($p_filter as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $orx = $qb->expr()->orx();
            switch ($key) {
                case 'status':
                    $mapper = array_flip(Notice::$status_enum);
                    foreach ($values as $value) {
                        if (isset($mapper[$value])) {
                            $orx->add($qb->expr()->eq('n.status', $mapper[$value]));
                        }
                    }
                    break;
            }
            $andx->add($orx);
        }
        return $andx;
    }

    /**
     * Get notices
     *
     * @return array
     */
    public function getNotices($hydration = 1, $queryParts)
    {
        $qb = $this->createQueryBuilder('n');

        if (isset($queryParts) && count($queryParts)) {
            $ids = array();
            foreach ($queryParts as $catId) {
                $ids[] = $catId;
            }

        }

        $qb->select('n')
           ->addSelect('cat')

            ->leftjoin('n.categories', 'cat');
        if (count($ids)) {
            $qb->andwhere($qb->expr()->in('cat.id', $ids));
            $qb->addGroupBy('n.id')->having($qb->expr()->gte($qb->expr()->count('cat.id'), count($ids)));
        }


        $now = new \DateTime('now');
        $test = $qb
            ->andWhere('n.published <= :published')
            ->setParameter('published', $now)
            ->andWhere('n.status <= :status')
            ->setParameter('status', 0)
            ->orderBy('n.id', 'DESC')
            ->getQuery();
 /*       var_dump($test->getSql());
        exit;*/


        return $test->getResult($hydration);
    }

    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    private function buildWhere(array $p_cols, $p_search, $qb)
    {
        $orx = $qb->expr()->orx();
        $orx->add($qb->expr()->like("n.lastname", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("n.firstname", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("n.title", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("n.body", $qb->expr()->literal("%{$p_search}%")));

        return $orx;
    }

}
