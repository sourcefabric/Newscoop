<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Publication,
    Newscoop\Entity\Subscription;

/**
 * Section repository
 */
class SectionRepository extends EntityRepository
{
    public function getSections($publication, $issue = false, $language = false)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\Section')
            ->createQueryBuilder('s')
            ->where('s.publication = :publication')
            ->setParameter('publication', $publication);

        if ($issue) {
            $queryBuilder->andWhere('s.issue = :issue')
                ->setParameter('issue', $issue);
        }

        $countQueryBuilder = clone $queryBuilder;
        $countQueryBuilder->select('count(s)');

        $count = $countQueryBuilder->getQuery()->getSingleScalarResult();

        $query = $queryBuilder->getQuery();
        $query->setHint('knp_paginator.count', $count);

        return $query;
    }

    public function getSectionsCountForPublication($publicationId)
    {
        $sectionsCount = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.publication = :publicationId')
            ->setParameter('publicationId', $publicationId)
            ->getQuery();

        return $sectionsCount;
    }

    /**
     * Get list of publication sections
     *
     * @param Newscoop\Entity\Publication $publication
     * @param Newscoop\Entity\Subscription $subscription;
     * @param bool $groupByLanguage
     * @return array
     */
    public function getAvailableSections(Publication $publication, Subscription $subscription, $groupByLanguage = false)
    {
        $em = $this->getEntityManager();

        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.language', 'l')
            ->where('s.publication = ?1');

        $groupBy = 's.number';
        if ($groupByLanguage) {
            $groupBy .= ', s.language';
        }
        $qb->groupBy($groupBy);

        $qb->setParameter(1, $publication);

        $sections = array();
        $subscribed = $subscription->getSections();
        foreach ($qb->getQuery()->getResult() as $section) {
            foreach ($subscribed as $pattern) { // filter subscribed sections
                if ($pattern->getSectionNumber() == $section->getNumber()) {
                    if ($groupByLanguage && !$pattern->getLanguageId()) { // filter same section
                        continue 2;
                    }

                    if (!$groupByLanguage && $pattern->getLanguageId() == $section->getLanguageId()) { // filter same section + language
                        continue 2;
                    }
                }
            }

            $sections[] = $section;
        }

        return $sections;
    }

    public function findSectionByArticle($article)
    {
        $em = $this->getEntityManager();

        $issue = $em->getRepository('Newscoop\Entity\Issue')
            ->findOneBy(array(
                'language' => $article->getLanguage(),
                'publication' => $article->getPublication(),
                'number' => $article->getIssueId(),
            ));

        if ($issue === null) {
            return null;
        }

        $section = $em->getRepository('Newscoop\Entity\Section')
            ->findOneBy(array(
                'language' => $article->getLanguage(),
                'publication' => $article->getPublication(),
                'issue' => $issue,
                'number' => $article->getSectionId(),
            ));

        return $section;
    }
}

