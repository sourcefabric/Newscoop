<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Mobile;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Article;

/**
 */
class IssueFacade
{
    const CURRENT_ISSUE = 'current';

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get current issue id
     *
     * @return string
     */
    public function getCurrentIssueId()
    {
        $issue = $this->find(self::CURRENT_ISSUE);
        return $issue ? $issue->getNumber() : null;
    }

    /**
     * Find all issues
     *
     * @param int $limit
     * @return array
     */
    public function findAll($limit = 100)
    {
        $qb = $this->createQueryBuilder();
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();

        return array_map(function ($row) {
            return $row->getArticle();
        }, $query->getResult());
    }

    /**
     * Find issue by given id
     *
     * @param mixed $id
     * @return Newscoop\Entity\Article
     */
    public function find($id)
    {
        if ($id === self::CURRENT_ISSUE) {
            $articles = $this->findAll(1);
            return !empty($articles) ? $articles[0] : null;
        }

        $qb = $this->createQueryBuilder();
        $qb->andWhere('a.number = :number')
            ->setParameter('number', $id);

        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        return $result ? $result->getArticle() : null;
    }

    /**
     * Get articles
     *
     * @param Newscoop\Entity\Article $issue
     * @return array
     */
    public function getArticles(Article $issue)
    {
        $articleList = $this->em->getRepository('Newscoop\Entity\RelatedArticleList')
            ->findOneBy(array(
                'number' => $issue->getNumber(),
            ));

        return $articleList ? $articleList->getArticles() : array();
    }

    /**
     * Test if given article is in current issue
     *
     * @param Newscoop\Entity\Article $article
     * @return bool
     */
    public function isInCurrentIssue(Article $article = null)
    {
        if ($article === null) {
            return false;
        }

        $issue = $this->find(self::CURRENT_ISSUE);
        if ($issue === null) {
            return false;
        }

        foreach ($this->getArticles($issue) as $issueArticle) {
            if ($issueArticle->getNumber() === $article->getNumber()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create query builder
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilder()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('i, a')
            ->from('Tageswoche\Entity\MobileIssueArticle', 'i')
            ->innerJoin('i.article', 'a')
            ->where('a.workflowStatus = :status')
            ->orderBy('i.date', 'DESC')
            ->setParameter('status', Article::STATUS_PUBLISHED);
        return $qb;
    }
}
