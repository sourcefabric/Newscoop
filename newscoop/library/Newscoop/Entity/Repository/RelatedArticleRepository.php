<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\RelatedArticles;
use Doctrine\ORM\Query;

/**
 * RelatedArticle repository
 */
class RelatedArticleRepository extends EntityRepository
{
    /**
     * Get all rlated articles for article and related articles container
     *
     * @param  RelatedArticles $relatedArticles
     * @param  integer         $articleNumber
     *
     * @return Query
     */
    public function getRelatedArticle($relatedArticles, $articleNumber)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.articleNumber = :articleNumber')
            ->andWhere('r.articleListId = :relatedArticlesId')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'relatedArticlesId' => $relatedArticles->getId()
            ));

        $query = $qb->getQuery();

        return $query;
    }

    /**
     * Get all related articles for related articles container, optionaly get only count of articles
     *
     * @param  RelatedArticles $relatedArticles
     * @param  boolean         $countOnly
     *
     * @return Query
     */
    public function getAllArticles($relatedArticles, $countOnly = false)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.articleListId = :articleListId')
            ->setParameters(array(
                'articleListId' => $relatedArticles->getId()
            ))
            ->orderBy('r.order', 'ASC');

        if ($countOnly) {
            return $qb->select('COUNT(r.id)')->getQuery();
        }

        $query = $qb->getQuery();

        return $query;
    }
}
