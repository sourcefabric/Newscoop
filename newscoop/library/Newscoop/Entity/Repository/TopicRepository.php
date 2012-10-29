<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Datatable\Source as DatatableSource;

/**
 * Author repository
 */
class TopicRepository extends DatatableSource
{

    public function getTopics()
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\Topic')
            ->createQueryBuilder('t');

        $countQueryBuilder = $em->getRepository('Newscoop\Entity\Topic')
            ->createQueryBuilder('t')
            ->select('count(t)');

        $topicsCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $topicsCount);
        
        return $query;
    }
}