<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleTopicRepository extends EntityRepository
{
    /**
     * Get all topics for an Article - Topic and article objects
     *
     * @param int|string $articleNumber Article number
     * @param boolean    $topicsOnly    If get only topics in the result
     *
     * @return Doctrine\ORM\Query
     */
    public function getArticleTopicsIds($articleNumber, $topicsOnly = false)
    {
        $queryBuilder = $this->createQueryBuilder('at');

        if ($topicsOnly) {
            $queryBuilder->select('IDENTITY(at.topic)');
        }

        $queryBuilder->where('at.article = :articleNumber')
            ->setParameter('articleNumber', $articleNumber);

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get all topics for an Article
     *
     * @param int|string $articleNumber Article number
     * @param string     $languageCode  Article's language code
     *
     * @return Doctrine\ORM\Query
     */
    public function getAllArticleTopics($articleNumber, $languageCode)
    {
        $languageId = $this->_em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($languageCode);

        $qb = $this->createQueryBuilder('at')
            ->select('at', 't')
            ->leftJoin('at.article', 'a')
            ->leftJoin('at.topic', 't')
            ->leftJoin('t.translations', 'tt')
            ->where('at.article = :articleNumber')
            ->andWhere('a.language = :languageId')
            ->setParameters(array(
                'articleNumber' => $articleNumber,
                'languageId' => $languageId
            ));

        $countQueryBuilder = clone $qb;
        $countQueryBuilder->select('COUNT(at)');

        $count = $countQueryBuilder->getQuery()->getSingleScalarResult();
        $query = $qb->getQuery();
        $query = $this->_em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->setTranslatableHint($query, $languageCode);
        $query->setHint('knp_paginator.count', $count);

        return $query;
    }

    /**
     * Gets the occurence of the topic
     *
     * @param string|int $topicId Topic id
     *
     * @return Doctrine\ORM\Query
     */
    public function getTheOccurrenceOfTheTopic($topicId)
    {
        $query = $this->createQueryBuilder('t')
            ->select('count(t)')
            ->where('t.topic = :topicId')
            ->setParameter('topicId', $topicId)
            ->getQuery();

        return $query;
    }
}
