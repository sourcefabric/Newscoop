<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use DateTime,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Datatable\Source as DatatableSource;

/**
 * Article repository
 */
class ArticleRepository extends DatatableSource
{
    public function getArticles($type = null, $language = null)
    {
        $em = $this->getEntityManager();
        $where = 'WHERE';

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a');

        $countQuery = 'SELECT COUNT(a) FROM Newscoop\Entity\Article a';

        if ($type) {
            $countQuery .= ' '.$where.' a.type = \''.$type.'\'';
            $queryBuilder->andWhere('a.type = :type')
                ->setParameter('type', $type);
            $where = 'AND';
        }

        if ($language) {
            $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

            $countQuery .= ' '.$where.' a.language = '.$languageId->getId();
            $queryBuilder->andWhere('a.language = :languageId')
                ->setParameter('languageId', $languageId->getId());
        }

        $articlesCount = $em
            ->createQuery($countQuery)
            ->getSingleScalarResult();

        $query = $queryBuilder
        ->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);
        
        return $query;
    }

    public function getArticle($number, $language = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a');

        $queryBuilder->where('a.number = :number')
            ->setParameter('number', $number);

        if ($language) {
            $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

            $queryBuilder->andWhere('a.language = :languageId')
                ->setParameter('languageId', $languageId->getId());
        }

        $query = $queryBuilder->getQuery();
        
        return $query;
    }
}
