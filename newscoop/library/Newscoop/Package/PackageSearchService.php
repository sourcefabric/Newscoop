<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

/**
 * Package Search Service
 */
class PackageSearchService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $orm;

    /**
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm)
    {
        $this->orm = $orm;
        $this->repository = $this->orm->getRepository('Newscoop\Package\Package');
    }

    /**
     * Perform a query
     *
     * @param string $query
     * @return array
     */
    public function find($query)
    {
        $qb = $this->repository->createQueryBuilder('p');

        $tokens = explode(' ', trim($query));
        foreach ($tokens as $p => $token) {
            $qb->orWhere($qb->expr()->like('p.headline', $qb->expr()->literal("%{$token}%")));
        }

        return $qb->getQuery()->getResult();
    }
}
