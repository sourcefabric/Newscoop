<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\SubscriptionSection;

/**
 * Publication repository
 */
class SubscriptionSectionRepository extends EntityRepository
{
    /**
     * Save
     *
     * @param Newscoop\Entity\SubscriptionSection $section
     * @param array $values
     * @return void
     */
    public function save(SubscriptionSection $section, array $values)
    {
        $em = $this->getEntityManager();

        $section
            ->setStartDate(new \DateTime($values['start_date']))
            ->setDays($values['days'])
            ->setPaidDays($values['paid_days']);

        $em->persist($section);
    }

    /**
     * Delete
     *
     * @param Newscoop\Entity\SubscriptionSection $section
     * @return void
     */
    public function delete(SubscriptionSection $section)
    {
        $em = $this->getEntityManager();

        $em->remove($section);
    }
}
