<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Article;
use Newscoop\Entity\RelatedArticles;
use Newscoop\Entity\RelatedArticle;
use Doctrine\ORM\EntityManager;

/**
 * Manage related articles
 */
class RelatedArticlesService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Get related articles for article
     *
     * @param Article $article
     */
    public function getRelatedArticles($article)
    {
        $relatedArticlesBox = $this->findRelatedArticlesBox($article);
        $relatedArticles = $this->em->getRepository('Newscoop\Entity\RelatedArticle')
            ->getAllArticles($relatedArticlesBox)
            ->getResult();

        return $relatedArticles;
    }

    /**
     * Remove related article from related articles container
     *
     * @param Article $article
     * @param Article $articleToRemove
     *
     * @return boolean
     */
    public function removeRelatedArticle($article, $articleToRemove)
    {
        $relatedArticles = $this->findRelatedArticlesBox($article);

        $relatedArticle = $this->em->getRepository('Newscoop\Entity\RelatedArticle')
            ->getRelatedArticle($relatedArticles, $articleToRemove->getNumber())
            ->getOneOrNullResult();

        if ($relatedArticle) {
            $this->em->remove($relatedArticle);
            $this->em->flush();

            $this->reorderAfterRemove($relatedArticles, $relatedArticle->getOrder());
        }

        return true;
    }

    /**
     * Add new related article to related articles container
     *
     * @param Article $article
     * @param Article $articleToAdd
     * @param integer $position
     *
     * @return boolean
     */
    public function addArticle($article, $articleToAdd, $position = false)
    {
        $relatedArticles = $this->findRelatedArticlesBox($article);

        $relatedArticle = $this->em->getRepository('Newscoop\Entity\RelatedArticle')
            ->getRelatedArticle($relatedArticles, $articleToAdd->getNumber())
            ->getOneOrNullResult();

        if ($relatedArticle) {
            $this->positionRelateArticle($relatedArticles, $relatedArticle, $position);

            return true;
        }

        $relatedArticle = new RelatedArticle($relatedArticles->getId(), $articleToAdd->getNumber());
        $this->em->persist($relatedArticle);
        $this->em->flush();

        $this->positionRelateArticle($relatedArticles, $relatedArticle, $position);

        return true;
    }

    private function reorderAfterRemove($relatedArticles, $removedRelatedArticlePosition)
    {
        $this->initOrderOnRelatedArticles($relatedArticles);

        try {
            $this->em->getConnection()->exec('LOCK TABLES context_articles WRITE;');

            // move all bigger than old position up (-1)
            $this->em
                ->createQuery('UPDATE Newscoop\Entity\RelatedArticle r SET r.order = r.order-1 WHERE r.order > :oldPosition')
                ->setParameter('oldPosition', $removedRelatedArticlePosition)
                ->execute();

            $this->em->getConnection()->exec('UNLOCK TABLES;');
        } catch (\Exception $e) {
            $this->em->getConnection()->exec('UNLOCK TABLES;');
        }

        return true;
    }

    private function initOrderOnRelatedArticles($relatedArticles)
    {
        $articles = $this->em->getRepository('Newscoop\Entity\RelatedArticle')
            ->getAllArticles($relatedArticles)
            ->getResult();

        $index = 0;
        foreach ($articles as $article) {
            if (is_int($article->getOrder()) && $article->getOrder() > 0 && $index == 0) {
                return;
            }
            $index++;

            $article->setOrder($index);
        }

        $this->em->flush();

        return true;
    }

    private function positionRelateArticle($relatedArticles, $relatedArticle, $position)
    {
        if ($position == false) {
            return;
        }

        $this->initOrderOnRelatedArticles($relatedArticles);

        try {
            $this->em->getConnection()->exec('LOCK TABLES context_articles WRITE, context_articles as c0_ WRITE;');

            // check if position isn't bigger that max one;
            $maxPosition = $this->em
                ->createQuery('SELECT COUNT(r) FROM Newscoop\Entity\RelatedArticle r WHERE r.articleListId = :articleListId AND r.order > 0 ORDER BY r.order ASC')
                ->setParameter('articleListId', $relatedArticles->getId())
                ->getSingleScalarResult();

            if ($position > ((int)$maxPosition)) {
                $position = (int)$maxPosition ;
            }

            // get article - move to position 0
            $oldOrder = $relatedArticle->getOrder();
            $relatedArticle->setOrder(0);
            $this->em->flush();

            // move all bigger than old position up (-1)
            $this->em
                ->createQuery('UPDATE Newscoop\Entity\RelatedArticle r SET r.order = r.order-1 WHERE r.order > :oldPosition')
                ->setParameter('oldPosition', $oldOrder)
                ->execute();

            // move all bigger than new position down (+1)
            $this->em
                ->createQuery('UPDATE Newscoop\Entity\RelatedArticle r SET r.order = r.order+1 WHERE r.order >= :newPosition')
                ->setParameter('newPosition', $position)
                ->execute();

            // move changed element from position 0 to new position
            $relatedArticle->setOrder($position);
            $this->em->flush();

            $this->em->getConnection()->exec('UNLOCK TABLES;');
        } catch (\Exception $e) {
            $this->em->getConnection()->exec('UNLOCK TABLES;');
        }
    }

    private function findRelatedArticlesBox($article)
    {
        $relatedArticles = $this->em->getRepository('Newscoop\Entity\RelatedArticles')
            ->getRelatedArticles($article->getNumber())
            ->getOneOrNullResult();

        if (!$relatedArticles) {
            return $this->createRelatedArticlesBox($article);
        }

        return $relatedArticles;
    }

    private function createRelatedArticlesBox($article)
    {
        $relatedArticlesBox = new RelatedArticles($article->getNumber());

        $this->em->persist($relatedArticlesBox);
        $this->em->flush();

        return $relatedArticlesBox;
    }
}