<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Datatable\Source as DatatableSource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Article repository
 */
class ArticleRepository extends DatatableSource
{
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

    public function getArticle($number, $language = null)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->select('a', 'p')
            ->leftJoin('a.packages', 'p');

        $queryBuilder->where('a.number = :number')
            ->setParameter('number', $number);

        if (!is_null($language)) {
            if (!is_numeric($language)) {
                $languageObject = $em->getRepository('Newscoop\Entity\Language')
                    ->findOneByCode($language);
            } else {
                $languageObject = $em->getRepository('Newscoop\Entity\Language')
                    ->findOneById($language);
            }

            if ($languageObject instanceof Newscoop\Entity\Language) {
                $queryBuilder->andWhere('a.language = :languageId')
                ->setParameter('languageId', $languageObject->getId());
            }
        }

        $query = $queryBuilder->getQuery();
        
        return $query;
    }

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
     * @param int $limit
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
     * Reset articles index
     *
     * @return void
     */
    public function resetIndex()
    {
        $query = $this->getEntityManager()
            ->createQuery('UPDATE Newscoop\Entity\Article a SET a.indexed = null, a.updated = a.updated');
        $query->execute();
    }

    public function setArticleIndexedNow($article)
    {
        $query = $this->getEntityManager()
                ->createQuery("UPDATE Newscoop\Entity\Article a SET a.indexed = :date, a.updated = a.updated WHERE a.number = :number AND a.language = :language")
                ->setParameters(array(
                    'date' => new \DateTime(), 
                    'number' => $article->getNumber(), 
                    'language' => $article->getLanguageId(), 
                ));

        $query->execute();
    }
}
