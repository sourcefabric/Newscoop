<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;

/**
 * Deletes inactive users after defined days
 */
class GarbageCollectionService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Run users garbage collection
     *
     * @param string $days
     *
     * @return void
     */
    public function run($days)
    {
        $this->gcUsers($days);
        $this->gcTokens();
    }

    /**
     * Remove obsolete users
     *
     * @param string $days
     *
     * @return void
     */
    private function gcUsers($days)
    {
        $query = $this->em->createQueryBuilder()
            ->delete('Newscoop\Entity\User', 'u')
            ->where('u.created < :ttl')
            ->andWhere('u.status = :status')
            ->getQuery();

        $query->setParameter('ttl', new \DateTime('-'.$days.' days'));
        $query->setParameter('status', User::STATUS_INACTIVE);
        $query->execute();
    }

    /**
     * Remove obsolete tokens
     *
     * @return void
     */
    private function gcTokens()
    {
        $query = $this->em->createQueryBuilder()
            ->delete('Newscoop\Entity\UserToken', 't')
            ->where('t.user NOT IN (SELECT u.id FROM Newscoop\Entity\User u)')
            ->getQuery();

        $query->execute();
    }
}
