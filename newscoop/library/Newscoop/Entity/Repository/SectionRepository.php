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
}

