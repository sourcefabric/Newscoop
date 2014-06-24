<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query;
use Newscoop\Datatable\Source as DatatableSource;

/**
 * Author repository
 */
class AuthorRepository extends DatatableSource
{
    /**
     * Get Author by Id
     *
     * @param integer $id
     *
     * @return Query
     */
    public function getAuthor($id)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Author')
            ->createQueryBuilder('a');

        $queryBuilder->where('a.id = :id')
            ->setParameter('id', $id);

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Perform a query
     *
     * @param string $query
     * @param array  $sort
     *
     * @return Query
     */
    public function searchAuthors($query, $sort = array())
    {
        $em = $this->getEntityManager();
        $qb = $em->getRepository('Newscoop\Entity\Author')->createQueryBuilder('a');
        $orX = $qb->expr()->orx();

        $orX->add($qb->expr()->like('a.first_name', $qb->expr()->literal("%{$query}%")));
        $orX->add($qb->expr()->like('a.last_name', $qb->expr()->literal("%{$query}%")));
        $qb->andWhere($orX);

        if ((!empty($sort)) && is_array($sort)) {
            foreach ($sort as $sortColumn => $sortDir) {
                $qb->addOrderBy('a.'.$sortColumn, $sortDir);
            }
        }

        return $qb->getQuery();
    }
}
