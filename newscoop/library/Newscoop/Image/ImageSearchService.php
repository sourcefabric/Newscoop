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
    protected $orm;

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
        $count = 0;

        $qb = $this->orm->getRepository('Newscoop\Image\LocalImage')->createQueryBuilder('i');
        $qb_count = $this->orm->getRepository('Newscoop\Image\LocalImage')->createQueryBuilder('i')
            ->select('COUNT(i)');

        $tokens_spec = $qb->expr()->orx();

        $tokens = explode(' ', trim($query));
        foreach ($tokens as $i => $token) {

            $tokens_spec->add($qb->expr()->like('i.description', $qb->expr()->literal("%{$token}%")));
        }

        if (!empty($tokens_spec)) {
            $qb->andWhere($tokens_spec);
            $qb_count->andWhere($tokens_spec);
        }

        if (is_array($criteria) && isset($criteria['source']) && is_array($criteria['source']) && (!empty($criteria['source']))) {

            $source_cases = array();
            foreach ($criteria['source'] as $one_source) {
                $source_cases[] = $one_source;
            }

            $qb->andwhere('i.source IN (:source)');
            $qb->setParameter('source', $source_cases);
            $qb_count->andwhere('i.source IN (:source)');
            $qb_count->setParameter('source', $source_cases);
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

        $count = 0 + (int) $qb_count->getQuery()->getSingleScalarResult();

        return $qb->getQuery()->getResult();
    }
}
