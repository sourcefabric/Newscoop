<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RelatedArticle repository
 */
class RelatedArticleRepository extends EntityRepository
{
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

    public function getAllArticles($relatedArticles)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.articleListId = :articleListId')
            ->setParameters(array(
                'articleListId' => $relatedArticles->getId()
            ));

        $query = $qb->getQuery();

        return $query;
    }
}
