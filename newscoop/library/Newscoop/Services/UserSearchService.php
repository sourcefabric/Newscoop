<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\Common\Persistence\ObjectManager;

/**
 */
class UserSearchService
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find users by given query
     *
     * @param string $q
     * @param int $limit
     * @return array
     */
    public function find($q, $limit = 25)
    {
        if (empty($q)) {
            return array();
        }

        $query = $this->em->createQueryBuilder()
            ->select('u')
            ->from('Newscoop\Entity\User', 'u')
            ->where('u.email LIKE ?0')
            ->orWhere('u.username LIKE ?0')
            ->orderBy('u.id', 'asc')
            ->setMaxResults($limit)
            ->getQuery();

        $query->setParameter(0, "%$q%");
        return $query->getResult();
    }
}
