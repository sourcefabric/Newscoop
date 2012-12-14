<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Datatable\Source as DatatableSource;

/**
 * Article repository
 */
class ArticleRepository extends DatatableSource
{
    /**
     * Get articles for indexing
     *
     * @param int $limit
     * @return array
     */
    public function getIndexBatch($limit = 50)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.indexed IS NULL')
            ->orWhere('a.indexed < a.updated')
            ->orderBy('a.indexed', 'asc')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Reset articles index
     *
     * @return void
     */
    public function resetIndex()
    {
        $query = $this->getEntityManager()
            ->createQuery('UPDATE Newscoop\Entity\Article a SET a.indexed = null, a.updated = a.updated');
        $query->execute();
    }
}
