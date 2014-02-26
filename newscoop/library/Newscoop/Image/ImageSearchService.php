<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Image Search Service
 */
class ImageSearchService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $orm;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Perform a query
     *
     * @param string $query
     * @return array
     */
    public function find($query, $criteria = null, $sort = null, $paging = null, &$count = null)
    {
        $qb = $this->orm->getRepository('Newscoop\Image\LocalImage')->createQueryBuilder('i');
        $andX = $qb->expr()->andX();

        if (is_numeric($query)) {
            $andX->add($qb->expr()->eq('i.user', $query));
        } else {
            $andX->add($qb->expr()->like('i.description', $qb->expr()->literal("%{$query}%")));
        }

        if (!empty($andX)) {
            $qb->andWhere($andX);
        }

        if (is_array($criteria) && isset($criteria['source']) && is_array($criteria['source']) && (!empty($criteria['source']))) {
            $orX = $qb->expr()->orx();
            foreach ($criteria['source'] as $oneSource) {
                $orX->add($qb->expr()->eq('i.source', $qb->expr()->literal($oneSource)));
            }

            $qb->andWhere($orX);
        }

        if ((!empty($sort)) && is_array($sort)) {
            foreach($sort as $sort_column => $sort_dir) {
                $qb->addOrderBy('i.'.$sort_column, $sort_dir);
            }
        }

        if ((!empty($paging)) && is_array($paging)) {
            if (isset($paging['length'])) {
                $qb->setMaxResults(0 + $paging['length']);
            }
            if (isset($paging['offset'])) {
                $qb->setFirstResult(0 + $paging['offset']);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
