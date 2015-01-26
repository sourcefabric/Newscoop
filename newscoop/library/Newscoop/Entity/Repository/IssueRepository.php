<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Publication;

/**
 * Issie repository
 */
class IssueRepository extends EntityRepository
{
    /**
     * Retrieve the latest issue. Optional an array for filtering can be
     * specified. Think of parameters: Publication, Languages, published or not,
     * etc.
     *
     * @param array $parameters Array containing filter options
     *
     * @return \Newscoop\Entity\Issue|null
     */
    public function getLatestByPublication($publicationId, $maxResults = 1)
    {
        $issuesIds = $this->createQueryBuilder('i')
            ->select('i.id')
            ->andWhere('i.publication = :publicationId')
            ->setParameter('publicationId', $publicationId)
            ->setMaxResults($maxResults)
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getArrayResult();

        if (count($issuesIds) == 0) {
            return false;
        }

        $ids = array();
        foreach ($issuesIds as $key => $issue) {
            $ids[] = $issue['id'];
        }

        $query = $this->createQueryBuilder('i')
            ->select('i', 'l', 's')
            ->andWhere('i.id IN (:ids)')
            ->leftJoin('i.language', 'l')
            ->leftJoin('i.sections', 's')
            ->setParameter('ids', $ids)
            ->orderBy('i.id', 'DESC')
            ->getQuery();

        return $query;
    }

    public function getOneOrNullIssue($languageCode, $publicationId, $shortName = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i', 'l')
            ->leftJoin('i.language' , 'l')
            ->where('l.code = :language')
            ->andWhere('i.publication = :publicationId')
            ->setParameters(array(
                'language' => $languageCode,
                'publicationId' => $publicationId
            ));

        if (!is_null($shortName)) {
            $qb->andWhere('i.shortName = :shortName')
                ->setParameter('shortName', $shortName);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
