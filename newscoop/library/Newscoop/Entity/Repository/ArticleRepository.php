<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Search\RepositoryInterface;
use Newscoop\NewscoopException\IndexException;
use Newscoop\Entity\Article;
use Newscoop\Entity\User;

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
}
