<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleType repository
 */
class ArticleTypeRepository extends EntityRepository
{
    public function getAllTypes()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb ->select('at')
            ->from('\Newscoop\Entity\ArticleType', 'at' )
            ->where("at.fieldName IS NULL OR at.fieldName = 'NULL'" );

        $countQueryBuilder = clone $qb;
        $countQueryBuilder->select('COUNT(at)');

        $count = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $qb->getQuery();
        $query->setHint('knp_paginator.count', $count);

        return $query;
    }
}