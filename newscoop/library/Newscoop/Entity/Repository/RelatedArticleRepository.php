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

    public function changeOneArticlePosition()
    {
/*        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $qb->update('Newscoop\Entity\Article', 'a')
                ->set('a.indexed', 'CURRENT_TIMESTAMP()');

        if (!is_null($articles) && count($articles) > 0) {
            $articleNumbers = array();

            foreach ($articles AS $article) {
                $articleNumbers[] = $article->getNumber();
            }

            $qb = $qb->where($qb->expr()->in('a.number',  $articleNumbers));
        }

        return $qb->getQuery()->execute();*/
    }
}
