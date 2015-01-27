<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Search\RepositoryInterface;
use Newscoop\NewscoopException\IndexException;
use Newscoop\Entity\Article;
use Newscoop\Entity\Language;
use Newscoop\Entity\User;
use Newscoop\NewscoopBundle\Entity\Topic;

/**
 * Article repository
 */
class ArticleRepository extends DatatableSource implements RepositoryInterface
{
    /**
     * Get All Articles from choosen publication (optional: article type and language)
     *
     * @param int    $publication Publication id
     * @param string $type        Article type name
     * @param int    $language    Language id
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticles($publication, $type = null, $language = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->where('a.workflowStatus = :workflowStatus')
            ->andWhere('a.publication = :publication')
            ->setParameters(array(
                'workflowStatus' => 'Y',
                'publication' => $publication
            ));

        $countQueryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.workflowStatus = :workflowStatus')
            ->andWhere('a.publication = :publication')
            ->setParameters(array(
                'workflowStatus' => 'Y',
                'publication' => $publication
            ));

        if ($type) {
            $countQueryBuilder->andWhere('a.type = :type')
                ->setParameter('type', $type);

            $queryBuilder->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        if ($language) {
            $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

            if (!$languageId) {
                throw new NotFoundHttpException('Results with language "'.$language.'" was not found.');
            }

            $countQueryBuilder->andWhere('a.language = :languageId')
                ->setParameter('languageId', $languageId->getId());

            $queryBuilder->andWhere('a.language = :languageId')
                ->setParameter('languageId', $languageId->getId());
        }

        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Search fo articles by keyword and filters
     *
     * @param  Language  $language
     * @param  array     $keywords
     * @param  integer   $publication   Publication Id
     * @param  integer   $issue         Issue Number
     * @param  integer   $section       Section Number
     * @param  boolean   $onlyPublished
     *
     * @return \Doctrine\ORM\Query
     */
    public function searchArticles($language, $keywords = array(), $publication = false, $issue = false, $section = false, $onlyPublished = true)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\ArticleIndex')->createQueryBuilder('a')
            ->select('DISTINCT(a.article) as number')
            ->leftJoin('a.keyword', 'k')
            ->leftJoin('a.article', 'aa');

        $orX = $queryBuilder->expr()->orx();

        foreach ($keywords as $keyword) {
            $orX->add($queryBuilder->expr()->like('k.keyword', $queryBuilder->expr()->literal("{$keyword}%")));
        }

        if (count($keywords) > 0) {
            $queryBuilder->andWhere($orX);
        }

        if ($publication) {
            $queryBuilder->andWhere('a.publication = :publication')
                ->setParameter('publication', $publication);
        }

        if ($section) {
            $queryBuilder->andWhere('a.sectionNumber = :section')
                ->setParameter('section', $section);
        }

        if ($issue) {
            $queryBuilder->andWhere('a.issueNumber = :issue')
                ->setParameter('issue', $issue);
        }

        $queryBuilder->setMaxResults(90);

        $articleNumbers = $queryBuilder->getQuery()->getResult();
        $tmpNumbers = array();
        foreach ($articleNumbers as $key => $value) {
            $tmpNumbers[] = $value['number'];
        }
        $articleNumbers = $tmpNumbers;

        $query = $this->getArticlesByIds($language, $articleNumbers, $onlyPublished);

        return $query;
    }

    public function getArticlesByIds($language, $ids = array(), $onlyPublished = true)
    {
        $em = $this->getEntityManager();

        $languageId = $em->getRepository('Newscoop\Entity\Language')
            ->findOneByCode($language);
        if (!$languageId) {
            throw new NotFoundHttpException('Results with language "'.$language.'" was not found.');
        }
        $language = $languageId;

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a, FIELD(a.number, :ids) as HIDDEN field')
            ->andWhere('a.number IN (:ids)')
            ->andWhere('a.language = :language')
            ->leftJoin('a.publication', 'p')
            ->leftJoin('a.issue', 'i')
            ->leftJoin('a.section', 's')
            ->orderBy('field')
            ->setParameters(array(
                'ids' => $ids,
                'language' => $language
            ));

        if ($onlyPublished) {
            $queryBuilder->andWhere('a.workflowStatus  = :workflowStatus')
                ->setParameter('workflowStatus', Article::STATUS_PUBLISHED);
        }

        $countQueryBuilder = clone $queryBuilder;
        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $countQueryBuilder->select('COUNT(a)')->orderBy('a.number')->getQuery()->getSingleScalarResult());

        return $query;
    }

    /**
     * Get Single Article
     *
     * @param int               $number   Article number
     * @param mixed[int|string] $language Language id or code
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticle($number, $language = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a', 'p', 'i', 's', 'l', 'u', 'ap')
            ->leftJoin('a.packages', 'p')
            ->leftJoin('a.issue', 'i')
            ->leftJoin('a.section', 's')
            ->leftJoin('a.language', 'l')
            ->leftJoin('a.lockUser', 'u')
            ->leftJoin('a.publication', 'ap');

        $queryBuilder->where('a.number = :number')
            ->setParameter('number', $number);

        if (!is_null($language)) {
            if (!is_numeric($language)) {
                $queryBuilder->andWhere('l.code = :code')
                    ->setParameter('code', $language);
            } else {
                $queryBuilder->andWhere('l.id = :id')
                    ->setParameter('id', $language);
            }
        }

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get Articles for choosen topic
     *
     * @param int     $publication
     * @param int     $topicId
     * @param int     $language
     * @param boolean $getResultAndCount
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForTopic($publication, $topicId, $language = false, $getResultAndCount = false)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a', 'att')
            ->where('att.id = :topicId')
            ->join('a.topics', 'att')
            ->setParameter('topicId', $topicId);

        $countQueryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->where('att.id = :topicId')
            ->join('a.topics', 'att')
            ->setParameter('topicId', $topicId);

        if ($language) {
            $queryBuilder->andWhere('att.language = :language')->setParameter('language', $language);
            $countQueryBuilder->andWhere('att.language = :language')->setParameter('language', $language);
        }

        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        if ($getResultAndCount) {
            return array(
                'result' => $query->getResult(),
                'count' => $articlesCount
            );
        }

        return $query;
    }

    /**
     * Get Articles for author
     *
     * @param \Newscoop\Entity\Author $author
     * @param \Newscoop\Criteria      $criteria
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForAuthor($author, \Newscoop\Criteria $criteria)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a')
            ->where('au.id = :author')
            ->andWhere('a.workflowStatus = :status')
            ->join('a.authors', 'au')
            ->setParameter('author', $author)
            ->setParameter('status', 'Y');

        if ($criteria->query) {
            $queryBuilder
                ->andWhere('a.name = :query')
                ->setParameter('query', $criteria->query);
        }

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->select('COUNT(a)');

        $queryBuilder->setMaxResults($criteria->maxResults);
        $queryBuilder->setFirstResult($criteria->firstResult);

        foreach ($criteria->orderBy as $key => $order) {
            $key = 'a.' . $key;
            $queryBuilder->orderBy($key, $order);
        }

        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Get Articles for author per day for choosen period back from now
     *
     * @param \Newscoop\Entity\Author $author
     * @param string                  $range
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForAuthorPerDay($author, $range = '-60 days')
    {
        $em = $this->getEntityManager();
        $date = new \DateTime();
        $date->modify($range);

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('COUNT(a.number) as number', "DATE_FORMAT(a.published, '%Y-%m-%d') as date")
            ->where('au.id = :author')
            ->andWhere('a.workflowStatus = :status')
            ->andWhere('a.published > :date')
            ->join('a.authors', 'au')
            ->setParameter('author', $author)
            ->setParameter('status', 'Y')
            ->setParameter('date', $date)
            ->groupBy('date');

        return $queryBuilder->getQuery();
    }

    /**
     * Get Articles for choosen section
     *
     * @param int $publication
     * @param int $sectionNumber
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForSection($publication, $sectionNumber)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a')
            ->where('a.section = :sectionNumber')
            ->setParameter('sectionNumber', $sectionNumber);

        $countQueryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.section = :sectionNumber')
            ->setParameter('sectionNumber', $sectionNumber);

        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Get number of articles assigned to Publication
     *
     * @param integer $publicationId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesCountForPublication($publicationId)
    {
        $articlesCount = $this->createQueryBuilder('a')
            ->select('COUNT(a.number)')
            ->andWhere('a.publication = :publicationId')
            ->setParameter('publicationId', $publicationId)
            ->getQuery();

        return $articlesCount;
    }

    /**
     * Get Articles for Playlist
     *
     * @param int $publication
     * @param int $playlistId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForPlaylist($publication, $playlistId)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a', 'ap')
            ->where('ap.id = :playlistId')
            ->join('a.playlists', 'ap')
            ->setParameter('playlistId', $playlistId);

        $countQueryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->where('ap.id = :playlistId')
            ->join('a.playlists', 'ap')
            ->setParameter('playlistId', $playlistId);

        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Get Article translations
     *
     * @param int $articleNumber
     * @param int $languageId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticleTranslations($articleNumber, $languageId)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a')
            ->where('a.number = :number')
            ->andWhere('a.language <> :language')
            ->setParameters(array(
                'number' => $articleNumber,
                'language' => $languageId
            ));

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get articles for indexing
     *
     * @param  int   $limit
     * @return array
     */
    public function getIndexBatch($limit = 50)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.indexed IS NULL')
            ->orWhere('a.indexed < a.updated')
            ->orderBy('a.indexed', 'asc')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get articles for indexing
     *
     * @param int   $count  Number of articles to index
     * @param array $filter Filter to apply to articles
     *
     * @return array
     */
    public function getBatch($count = self::BATCH_COUNT, array $filter = null)
    {
        $qb = $this->createQueryBuilder('a');

        if (is_null($filter)) {
            $qb->where('a.indexed IS NULL')
                ->orWhere('a.indexed < a.updated')
                ->orderBy('a.number', 'DESC');
        } else {
            throw new IndexException("Filter is not implemented yet.");
        }

        if (is_numeric($count)) {
            $qb->setMaxResults($count);
        }

        $batch = $qb->getQuery()
            ->getResult();

        return $batch;
    }

    /**
     * Set indexed now
     *
     * @param  array $articles
     * @return void
     */
    public function setIndexedNow(array $articles)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $qb->update('Newscoop\Entity\Article', 'a')
                ->set('a.indexed', 'CURRENT_TIMESTAMP()');

        if (!is_null($articles) && count($articles) > 0) {
            $articleNumbers = array();

            foreach ($articles AS $article) {
                $articleNumbers[] = $article->getNumber();
            }

            $qb = $qb->where($qb->expr()->in('a.number',  $articleNumbers));
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Set indexed null
     *
     * @return void
     */
    public function setIndexedNull(array $articles = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $qb->update('Newscoop\Entity\Article', 'a')
                ->set('a.indexed', 'NULL');

        if (!is_null($articles) && count($articles) > 0) {
            $articleNumbers = array();

            foreach ($articles AS $article) {
                $articleNumbers[] = $article->getNumber();
            }

            $qb = $qb->where($qb->expr()->in('a.number',  $articleNumbers));
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Get articles count for user if is author
     *
     * @param Newscoop\Entity\User $user
     *
     * @return int
     */
    public function countByAuthor(User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(a)')
            ->from('Newscoop\Entity\Article', 'a')
            ->from('Newscoop\Entity\ArticleAuthor', 'aa')
            ->from('Newscoop\Entity\User', 'u')
            ->where('a.number = aa.articleNumber')
            ->andWhere('a.language = aa.languageId')
            ->andWhere('aa.author = u.author')
            ->andwhere('u.id = :user')
            ->andWhere($qb->expr()->in('a.type', array('news', 'blog')))
            ->andWhere('a.workflowStatus = :status')
            ->setParameters(array(
                'user' => $user->getId(),
                'status' => Article::STATUS_PUBLISHED
            ));

        $count = $qb->getQuery()->getSingleScalarResult();

        return (int) $count;
    }

    /**
     * Get new minimal article order value
     *
     * @param integer $publication
     * @param integer $issue
     * @param integer $section
     *
     * @return \Doctrine\ORM\Query
     */
    public function getMinArticleOrder($publication = null, $issue = null, $section = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('MIN(a.articleOrder)')
            ->from('Newscoop\Entity\Article', 'a');

        if ($publication) {
            $qb->andWhere('a.publication = :publication')
                ->setParameter('publication', $publication);
        }

        if ($issue) {
            $qb->andWhere('a.issueId = :issue')
                ->setParameter('issue', $issue->getId());
        }

        if ($section) {
            $qb->andWhere('a.sectionId = :section')
                ->setParameter('section', $section->getId());
        }

        return $qb->getQuery();
    }

    /**
     * Update article order
     *
     * @param integer $increment
     * @param integer $publication
     * @param integer $issue
     * @param integer $section
     *
     * @return \Doctrine\ORM\Query
     */
    public function updateArticleOrder($increment, $publication = null, $issue = null, $section = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('a');

        $qb->update('Newscoop\Entity\Article', 'a');

        if ($publication) {
            $qb->andWhere('a.publication = :publication')
                ->setParameter('publication', $publication);
        }

        if ($issue) {
            $qb->andWhere('a.issueId = :issue')
                ->setParameter('issue', $issue->getId());
        }

        if ($section) {
            $qb->andWhere('a.sectionId = :section')
                ->setParameter('section', $section->getId());
        }

        $qb->set('a.articleOrder', 'a.articleOrder + :increment')
            ->setParameter('increment', $increment);

        return $qb->getQuery();
    }
}
