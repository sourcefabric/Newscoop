<?php
/**
 * @package Newscoop
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Event;

/**
 * Log repository
 */
class LogRepository extends EntityRepository
{
    /**
     * Get logs
     *
     * @param int $offset
     * @param int $limit
     * @param \Newscoop\Entity\Event|null $event
     * @return array
     */
    public function getLogs($offset, $limit, Event $event = NULL)
    {
        $qb = $this->createQueryBuilder('l');

        if (isset($event)) {
            $qb->where('l.event = :event')
                ->setParameter('event', $event->getId());
        }

        return $qb->orderBy('l.time_created', 'DESC')
            ->setFirstResult((int) $offset)
            ->setMaxResults((int) $limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get logs count
     *
     * @param \Newscoop\Entity\Event|null $event
     * @return int
     */
    public function getCount(Event $event = NULL)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(l)')
            ->from('Newscoop\Entity\Log', 'l');

        if (isset($event)) {
            $qb->where('l.event = :event')
                ->setParameter('event', $event->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
