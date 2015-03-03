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
            ->orderBy('i.id', 'DESC');

        if ($maxResults) {
            $issuesIds->setMaxResults($maxResults);
        }

        $issuesIds = $issuesIds->getQuery()
            ->getArrayResult();

        if (count($issuesIds) == 0) {
            return null;
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

    public function getByPublicationAndNumberAndLanguage($publication, $number, $language)
    {
        $issue = $this->createQueryBuilder('i')
            ->andWhere('i.publication = :publication')
            ->setParameter('publication', $publication)
            ->andWhere('i.number = :number')
            ->setParameter('number', $number)
            ->andWhere('i.language = :language')
            ->setParameter('language', $language)
            ->getQuery();

        return $issue;
    }

    public function getIssuesCountForPublication($publicationId)
    {
        $issuesCount = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.publication = :publicationId')
            ->setParameter('publicationId', $publicationId)
            ->getQuery();

        return $issuesCount;
    }

    public function getIssue($languageCode, $publication, $shortName = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i', 'l')
            ->leftJoin('i.language' , 'l')
            ->where('l.code = :language')
            ->andWhere('i.publication = :publicationId')
            ->setParameters(array(
                'language' => $languageCode,
                'publicationId' => $publication
            ));

        if (!is_null($shortName)) {
            $qb->andWhere('i.shortName = :shortName')
                ->setParameter('shortName', $shortName);
        }

        return $qb->getQuery();
    }
}
