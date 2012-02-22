<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Image Search Service
 */
class ImageSearchService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $orm;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Perform a query
     *
     * @param string $query
     * @return array
     */
    public function find($query)
    {
        $qb = $this->orm->getRepository('Newscoop\Image\LocalImage')->createQueryBuilder('i');

        $tokens = explode(' ', trim($query));
        foreach ($tokens as $i => $token) {
            $qb->orWhere($qb->expr()->like('i.description', $qb->expr()->literal("%{$token}%")));
        }

        return $qb->getQuery()->getResult();
    }
}
