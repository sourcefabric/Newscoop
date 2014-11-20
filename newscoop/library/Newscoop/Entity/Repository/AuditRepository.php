<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\AuditEvent;

/**
 * Audit repository.
 */
class AuditRepository extends EntityRepository
{
    public function save(AuditEvent $event, array $values)
    {
        $em = $this->getEntityManager();

        $event->setResourceId($values['resource_id']);
        $event->setResourceType($values['resource_type']);
        $event->setResourceTitle($values['resource_title']);
        $event->setResourceDiff($values['resource_diff']);
        $event->setAction($values['action']);

        if (!empty($values['user'])) {
            $user = is_int($values['user']) ? $em->getReference('Newscoop\Entity\User', $values['user']) : $values['user'];
            $event->setUser($user);
        }

        $em->persist($event);
        $em->flush();
    }
    
    /**
     * Get event count
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(a)')
            ->from($this->getEntityName(), 'a');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("a.$property = :$property");
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
     * Get events to archive
     *
     * @param \DateTime $date
     * @return array
     */
    public function getFlushableEvents(\DateTime $date)
    {
        $date = $date->format('Y-m-d H:i:s');

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityName(), 'e')
            ->where('e.created < :when')
            ->setParameters(array('when' => $date));

        return $query->getQuery()->getResult();
    }
}
