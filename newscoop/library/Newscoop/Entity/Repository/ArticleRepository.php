<?php

/**
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Search\RepositoryInterface;
use Newscoop\NewscoopException\IndexException;
use Newscoop\Entity\Article;
use Newscoop\Entity\Language;
use Newscoop\Entity\User;
use Newscoop\NewscoopBundle\Entity\Topic;

/**
 * Article repository.
 */
class ArticleRepository extends DatatableSource implements RepositoryInterface
{
    /**
     * Get All Articles from choosen publication (optional: article type and language).
     *
     * @param int    $publication Publication id
     * @param string $type        Article type name
     * @param int    $language    Language id
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticles($publication, $type = null, $language = null, $issue = null, $section = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->where('a.workflowStatus = :workflowStatus')
            ->andWhere('a.publication = :publication')
            ->setParameters(array(
                'workflowStatus' => 'Y',
                'publication' => $publication,
            ));

        if ($type) {
            $queryBuilder->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }

        if ($issue) {
            $queryBuilder->andWhere('a.issueId = :issue')
                ->setParameter('issue', $issue);
        }

        if ($section) {
            $queryBuilder->andWhere('a.sectionId = :section')
                ->setParameter('section', $section);
        }

        if ($language) {
            $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

            if (!$languageId) {
                throw new NotFoundHttpException('Results with language "'.$language.'" was not found.');
            }

            $queryBuilder->andWhere('a.language = :languageId')
                ->setParameter('languageId', $languageId->getId());
        }

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->select('count(a)');
        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Search fo articles by keyword and filters.
     *
     * @param Language $language
     * @param array    $keywords
     * @param int      $publication   Publication Id
     * @param int      $issue         Issue Number
     * @param int      $section       Section Number
     * @param bool     $onlyPublished
     *
     * @return \Doctrine\ORM\Query
     */
    public function searchArticles($articleSearchCriteria, $onlyPublished = true, $order = 'desc')
    {
        $articleNumbers = array();
        if ($articleSearchCriteria->query) {
            $em = $this->getEntityManager();
            $getLastArticles = true;
            $queryBuilder = $em->getRepository('Newscoop\Entity\ArticleIndex')->createQueryBuilder('a')
                ->select('DISTINCT(a.article) as number');

            $orX = $queryBuilder->expr()->orx();

            $keywords = array_diff(explode(' ', $articleSearchCriteria->query), array(''));
            foreach ($keywords as $keyword) {
                $orX->add($queryBuilder->expr()->like('k.keyword', $queryBuilder->expr()->literal("{$keyword}%")));
            }

            if (count($keywords) > 0) {
                $queryBuilder->leftJoin('a.keyword', 'k')
                    ->andWhere($orX);
                $getLastArticles = false;
            }

            if ($articleSearchCriteria->publication) {
                $queryBuilder->andWhere('a.publication = :publication')
                    ->setParameter('publication', $articleSearchCriteria->publication);
                $getLastArticles = false;
            }

            if ($articleSearchCriteria->section) {
                $queryBuilder->andWhere('a.sectionNumber = :section')
                    ->setParameter('section', $articleSearchCriteria->section);
                $getLastArticles = false;
            }

            if ($articleSearchCriteria->issue) {
                $queryBuilder->andWhere('a.issueNumber = :issue')
                    ->setParameter('issue', $articleSearchCriteria->issue);
                $getLastArticles = false;
            }

            if ($articleSearchCriteria->language) {
                $languageId = $em->getRepository('Newscoop\Entity\Language')
                    ->findOneByCode($articleSearchCriteria->language);
                if ($languageId) {
                    $queryBuilder->andWhere('a.language = :language')
                        ->setParameter('language', $languageId);
                    $getLastArticles = false;
                }
            }

            if ($getLastArticles) {
                $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
                    ->createQueryBuilder('a')
                    ->select('a.number as number')
                    ->orderBy('a.uploaded', 'DESC');
            }

            $queryBuilder->setMaxResults(80)
                ->orderBy('number', $order);

            $articleNumbers = $queryBuilder->getQuery()->getResult();
            $tmpNumbers = array();
            foreach ($articleNumbers as $key => $value) {
                $tmpNumbers[] = $value['number'];
            }

            $articleNumbers = $tmpNumbers;
        }

        $query = $this->getArticlesByCriteria($articleSearchCriteria, $articleNumbers, $onlyPublished, $order);

        return $query;
    }

    public function getArticlesByCriteria($articleSearchCriteria, $ids = array(), $onlyPublished = true, $order = 'desc')
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
                ->createQueryBuilder('a');

        if ($articleSearchCriteria->query && !empty($ids)) {
            $queryBuilder->select('a, FIELD(a.number, :ids) as HIDDEN field', 'l', 'u', 'ap', 'p', 'aa', 'au', 't')
                ->andWhere('a.number IN (:ids)')
                ->setParameters(array(
                    'ids' => $ids,
                ));
        } else {
            $queryBuilder->select('a', 'l', 'u', 'ap', 'p', 'aa', 'au', 't');
            if ($articleSearchCriteria->publication) {
                $queryBuilder->andWhere('a.publication = :publication')
                    ->setParameter('publication', $articleSearchCriteria->publication);
            }

            if ($articleSearchCriteria->section) {
                $queryBuilder->andWhere('a.sectionId = :section')
                    ->setParameter('section', $articleSearchCriteria->section);
            }

            if ($articleSearchCriteria->issue) {
                $queryBuilder->andWhere('a.issueId = :issue')
                    ->setParameter('issue', $articleSearchCriteria->issue);
            }
        }

        $queryBuilder->leftJoin('a.issue', 'i')
            ->leftJoin('a.section', 's')
            ->leftJoin('a.packages', 'p')
            ->leftJoin('a.language', 'l')
            ->leftJoin('a.lockUser', 'u')
            ->leftJoin('a.publication', 'ap')
            ->leftJoin('a.attachments', 'aa')
            ->leftJoin('a.authors', 'au')
            ->leftJoin('a.topics', 't');

        if ($articleSearchCriteria->language) {
            $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($articleSearchCriteria->language);
            if ($languageId) {
                $queryBuilder->andWhere('a.language = :language')
                    ->setParameter('language', $languageId);
            }
        }

        if ($articleSearchCriteria->article_type) {
            $queryBuilder->andWhere('a.type = :article_type')
                ->setParameter('article_type', $articleSearchCriteria->article_type);
        }

        if ($articleSearchCriteria->publish_date) {
            $startDate = new \DateTime($articleSearchCriteria->publish_date);
            $endDate = new \DateTime($articleSearchCriteria->publish_date);
            $endDate->modify('+ 1 day');

            $queryBuilder->andWhere('a.published >= :publish_date_start')
                ->setParameter('publish_date_start', $startDate);
            $queryBuilder->andWhere('a.published < :publish_date_end')
                ->setParameter('publish_date_end', $endDate);
        }

        if ($articleSearchCriteria->published_after) {
            $queryBuilder->andWhere('a.published > :published_after')
                ->setParameter('published_after', $articleSearchCriteria->published_after);
        }

        if ($articleSearchCriteria->published_before) {
            $queryBuilder->andWhere('a.published < :published_before')
                ->setParameter('published_before', $articleSearchCriteria->published_before);
        }

        if ($articleSearchCriteria->author) {
            $queryBuilder->andWhere('au.id = :author')
                ->setParameter('author', $articleSearchCriteria->author);
        }

        if ($articleSearchCriteria->creator) {
            $queryBuilder->andWhere('a.creator = :creator')
                ->setParameter('creator', $articleSearchCriteria->creator);
        }

        if ($articleSearchCriteria->status) {
            $queryBuilder->andWhere('a.workflowStatus = :status')
                ->setParameter('status', $articleSearchCriteria->status);
        }

        if ($articleSearchCriteria->topic) {
            $queryBuilder->andWhere('t.id = :topic')
                ->setParameter('topic', $articleSearchCriteria->topic);
        }

        if ($onlyPublished) {
            $queryBuilder->andWhere('a.workflowStatus  = :workflowStatus')
                ->setParameter('workflowStatus', Article::STATUS_PUBLISHED);
        }

        if ($order != false) {
            $queryBuilder->orderBy('a.uploaded', $order);
        }

        $countQueryBuilder = clone $queryBuilder;
        $query = $queryBuilder->getQuery();
        $countQueryBuilder->resetDQLPart('join');
        $query->setHint('knp_paginator.count', $countQueryBuilder->select('COUNT(a)')->getQuery()->getSingleScalarResult());

        return $query;
    }

    /**
     * Get Single Article.
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
     * Get Articles for choosen topic.
     *
     * @param int  $publication
     * @param int  $topicId
     * @param int  $language
     * @param bool $getResultAndCount
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForTopic($publication, $topicId, $language = false, $getResultAndCount = false, $order = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a', 'att', 'p', 'i', 's', 'l', 'u', 'ap')
            ->leftJoin('a.packages', 'p')
            ->leftJoin('a.issue', 'i')
            ->leftJoin('a.section', 's')
            ->leftJoin('a.language', 'l')
            ->leftJoin('a.lockUser', 'u')
            ->leftJoin('a.publication', 'ap')
            ->where('att.id = :topicId')
            ->join('a.topics', 'att')
            ->setParameter('topicId', $topicId);

        if ($order !== null) {
            $queryBuilder->orderBy('a.published', $order);
        }

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
                'count' => $articlesCount,
            );
        }

        return $query;
    }

    /**
     * Get Articles for author.
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
            $key = 'a.'.$key;
            $queryBuilder->orderBy($key, $order);
        }

        $articlesCount = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Get Articles for author per day for choosen period back from now.
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
     * Get Articles for chosen section.
     *
     * @param int           $publication
     * @param int           $sectionNumber
     * @param Language|null $sectionLanguage
     *
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesForSection($publication, $sectionNumber, $sectionLanguage = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->where('a.sectionId = :sectionNumber')
            ->andWhere('a.language = :sectionLanguage')
            ->andWhere('a.publication = :publicationId')
            ->setParameters(array(
                'sectionNumber' => $sectionNumber,
                'sectionLanguage' => $sectionLanguage,
                'publicationId' => $publication,
            ));

        $qbCount = clone $queryBuilder;
        $qbCount->select('count(a)');
        $articlesCount = $qbCount->getQuery()->getSingleScalarResult();
        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $articlesCount);

        return $query;
    }

    /**
     * Get number of articles assigned to Publication.
     *
     * @param int $publicationId
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
     * Get Articles for Playlist.
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
     * Get Article translations.
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
                'language' => $languageId,
            ));

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get articles for indexing.
     *
     * @param int $limit
     *
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
     * Get articles for indexing.
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
            throw new IndexException('Filter is not implemented yet.');
        }

        if (is_numeric($count)) {
            $qb->setMaxResults($count);
        }

        $batch = $qb->getQuery()
            ->getResult();

        return $batch;
    }

    /**
     * Set indexed now.
     *
     * @param array $articles
     */
    public function setIndexedNow(array $articles)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $qb->update('Newscoop\Entity\Article', 'a')
                ->set('a.indexed', 'CURRENT_TIMESTAMP()');

        if (!is_null($articles) && count($articles) > 0) {
            $articleNumbers = array();

            foreach ($articles as $article) {
                $articleNumbers[] = $article->getNumber();
            }

            $qb = $qb->where($qb->expr()->in('a.number',  $articleNumbers));
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Set indexed null.
     */
    public function setIndexedNull(array $articles = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $qb->update('Newscoop\Entity\Article', 'a')
                ->set('a.indexed', 'NULL');

        if (!is_null($articles) && count($articles) > 0) {
            $articleNumbers = array();

            foreach ($articles as $article) {
                $articleNumbers[] = $article->getNumber();
            }

            $qb = $qb->where($qb->expr()->in('a.number',  $articleNumbers));
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Get articles count for user if is author.
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
                'status' => Article::STATUS_PUBLISHED,
            ));

        $count = $qb->getQuery()->getSingleScalarResult();

        return (int) $count;
    }

    /**
     * Get new minimal article order value.
     *
     * @param int $publication
     * @param int $issue
     * @param int $section
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
     * Update article order.
     *
     * @param int $increment
     * @param int $publication
     * @param int $issue
     * @param int $section
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
