<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Mobile;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\Article;

/**
 */
class IssueFacade
{
    const ARTICLE_TYPE = 'mobile_issue';
    const CURRENT_ISSUE = 'current';
    const LIMIT = 200;

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
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()->findBy($this->getCriteria(), $this->getOrderBy(), self::LIMIT);
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
            $issues = $this->getRepository()->findBy($this->getCriteria(), $this->getOrderBy(), 1);
            return empty($issues) ? null : $issues[0];
        }

        return $this->getRepository()->findOneBy(array(
            'number' => (int) $id,
        ));
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
     * Get issue repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Article');
    }

    /**
     * Get criteria for issues
     *
     * @return array
     */
    private function getCriteria()
    {
        return array(
            'type' => self::ARTICLE_TYPE,
            'workflowStatus' => Article::STATUS_PUBLISHED,
        );
    }

    /**
     * Get order by for issues
     *
     * @return array
     */
    private function getOrderBy()
    {
        return array(
            'number' => 'DESC',
        );
    }
}
