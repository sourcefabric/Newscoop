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

/**
 * Manage related articles
 */
class RelatedArticlesService
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getRelatedArticles($article)
    {
        $relatedArticlesBox = $this->findRelatedArticlesBox($article);
        $relatedArticles = $this->em->getRepository('Newscoop\Entity\RelatedArticle')
            ->getAllArticles($relatedArticlesBox)
            ->getResult();

        return $relatedArticles;
    }

    public function removeRelatedArticle($article, $articleToRemove)
    {
        $relatedArticles = $this->findRelatedArticlesBox($article);

        $relatedArticle = $this->em->getRepository('Newscoop\Entity\RelatedArticle')
            ->getRelatedArticle($relatedArticles, $articleToRemove->getNumber())
            ->getOneOrNullResult();

        if ($relatedArticle) {
            $this->em->remove($relatedArticle);
            $this->em->flush();

            $this->reorderAfterRemove($relatedArticles, $relatedArticle);
        }

        return true;
    }

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

        return true;
    }

    private function reorderAfterRemove($relatedArticles, $removedRelatedArticle)
    {
        // find possition of removed articles and change all next with "position -1".
    }

    private function positionRelateArticle($relatedArticles, $relatedArticle, $position)
    {
        // if article is already in related articles
        // http://stackoverflow.com/questions/12624153/move-an-array-element-to-a-new-index-in-php
        // if it's new and position is specified
        // http://stackoverflow.com/questions/3797239/insert-new-item-in-array-on-any-position-in-php

        // add new field to related article - position

        // when adding new article with precised position update segments of articles with lower and bigger possition (than new one).
        // This will allow for rendering only few lats articles in playlist and related articles boxes (with load more option)
        // and still allowing updating list order and adding new elements
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