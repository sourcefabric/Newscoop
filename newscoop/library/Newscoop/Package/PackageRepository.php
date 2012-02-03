<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

/**
 * Package repository
 */
class PackageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find packages by a set of criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p, COUNT(i)')
            ->leftJoin('p.items', 'i')
            ->groupBy('p.id');

        $this->setCriteria($queryBuilder, $criteria);
        $queryBuilder->setFirstResult($offset);
        $queryBuilder->setMaxResults($limit);

        $packages = array();
        foreach ($queryBuilder->getQuery()->getResult() as $row) {
            $package = $row[0];
            $package->setItemsCount($row[1]);
            $packages[] = $package;
        }

        return $packages;
    }

    /**
     * Get count of packages by a set of criteria
     *
     * @param array $criteria
     * @return int
     */
    public function getCountBy(array $criteria)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p)');

        $this->setCriteria($queryBuilder, $criteria);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Find available for article
     *
     * @param Newscoop\Package\Article $article
     * @return array
     */
    public function findAvailableForArticle(Article $article)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $attachedIds = array_map(function($package) { return $package->getId(); }, $article->getPackages()->toArray());
        if (!empty($attachedIds)) {
            $queryBuilder->where($queryBuilder->expr()->notIn('p.id', implode(', ', $attachedIds)));
        }
        $queryBuilder->orderBy('p.id', 'desc');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Set criteria for query builder
     *
     * @param Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $criteria
     * @return void
     */
    private function setCriteria(\Doctrine\ORM\QueryBuilder $queryBuilder, array $criteria)
    {
        foreach ($criteria as $property => $value) {
            $queryBuilder->andWhere(sprintf('p.%s = :%s', $property, $property));
            $queryBuilder->setParameter($property, $value);
        }
    }
}
