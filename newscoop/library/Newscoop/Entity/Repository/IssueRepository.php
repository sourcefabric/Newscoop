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
     * @param array       $parameters Array containing filter options
     * @param boolean|int $maxResults Max results to show
     * @param boolean     $leftJoins  Adds left joins to the query to get section and language
     *
     * @return \Newscoop\Entity\Issue|null
     */
    public function getLatestByPublication($publicationId, $maxResults = 1, $leftJoins = true)
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
            return;
        }

        $ids = array();
        foreach ($issuesIds as $key => $issue) {
            $ids[] = $issue['id'];
        }

        $query = $this->createQueryBuilder('i')
            ->where('i.id IN (:ids)')
            ->setParameter('ids', $ids);

        if ($leftJoins) {
            $query->select('i', 'l', 's')
                ->leftJoin('i.language', 'l')
                ->leftJoin('i.sections', 's');
        }

        $query->orderBy('i.id', 'DESC');

        return $query->getQuery();
    }

    public function getByPublicationAndNumberAndLanguage($publication, $number = null, $language = null)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->where('i.publication = :publication')
            ->setParameter('publication', $publication);

        if ($number) {
            $queryBuilder->andWhere('i.number = :number')
                ->setParameter('number', $number);
        }

        if ($language) {
            $queryBuilder->andWhere('i.language = :language')
                ->setParameter('language', $language);
        }

        return $queryBuilder->getQuery();
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
            ->leftJoin('i.language', 'l')
            ->where('l.code = :language')
            ->andWhere('i.publication = :publicationId')
            ->setParameters(array(
                'language' => $languageCode,
                'publicationId' => $publication,
            ));

        if (!is_null($shortName)) {
            $qb->andWhere('i.shortName = :shortName')
                ->setParameter('shortName', $shortName);
        }

        return $qb->getQuery();
    }

    public function getLastPublishedByPublicationAndLanguage($publicationId, $languageId)
    {
        $query = $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.publication = :publicationId')
            ->andWhere('i.workflowStatus = :publishStatus')
            ->andWhere('i.language = :languageId')
            ->setParameter('publicationId', $publicationId)
            ->setParameter('publishStatus', \Newscoop\Entity\Issue::STATUS_PUBLISHED)
            ->setParameter('languageId', $languageId)
            ->setMaxResults(1)
            ->orderBy('i.published', 'ASC');

        return $query->getQuery();
    }
}
