<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Rating;
use Newscoop\Datatable\Source as DatatableSource;

/**
 * Rating repository
 */
class RatingRepository extends DatatableSource
{

    /**
     * Get new instance of the rating
     *
     * @return \Newscoop\Entity\Rating
     */
    public function getPrototype()
    {
        return new Rating;
    }

    /**
     * Method for saving a rating
     *
     * @param Rating $p_enitity
     * @param array $params
     * @return Rating $p_enitity
     */
    public function save(Rating $p_entity, $p_values)
    {
	    // get the enitity manager
        $em = $this->getEntityManager();

        $p_entity->setUserId($p_values['user_id'])
				 ->setArticleId($p_values['article_id'])
				 ->setRatingScore($p_values['rating_score'])
				 ->setTimeCreated($p_values['time_created']);

        $em->persist($p_entity);

        return $p_entity;
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }


    /**
     * Get rating stats for one article
     *
     * @param int articleId
     * @return array
     */
    public function getArticleRating($articleId)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('r.articleId, COUNT(r.id) AS number_votes, AVG(r.ratingScore) AS avg_score, SUM(r.ratingScore) AS total_score')
            ->from($this->getEntityName(), 'r')
            ->where('r.articleId = :articleId')
            ->groupBy('r.articleId');

        $query = $queryBuilder->getQuery()
            ->setParameter('articleId', $articleId);

        return $query->getScalarResult();
    }

    /**
     * Get rating count
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from($this->getEntityName(), 'u');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("u.$property = :$property");
            }
        }

        $query = $queryBuilder->getQuery();
        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $query->setParameter($property, $value);
            }
        }

        return (int) $query->getSingleScalarResult();

    }

    /**
     * Get total rating score
     *
     * @param array $criteria
     * @return int
     */
    public function totalRatingBy(array $criteria)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.ratingScore)')
            ->from($this->getEntityName(), 'u');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("u.$property = :$property");
            }
        }

        $query = $queryBuilder->getQuery();
        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $query->setParameter($property, $value);
            }
        }

        return (int) $query->getSingleScalarResult();
    }
}
