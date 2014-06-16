<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\User,
    Newscoop\Entity\Author;

/**
 * Author service
 */
class AuthorService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Newscoop\Entity\Author');
    }

    /**
     * Get authors
     *
     * @param  string $term  Search term
     * @param  int    $limit Max results
     *
     * @return array
     */
    public function getAuthors($term, $limit)
    {
        $qb = $this->em->createQueryBuilder();
        $qbUsers = clone $qb;

        $qb->select("trim(concat(aa.first_name, concat(' ', aa.last_name))) as name")
            ->from('Newscoop\Entity\Author', 'aa')
            ->where($qb->expr()->like('aa.last_name', ':term'))
            ->orWhere($qb->expr()->like('aa.first_name', ':term'))
            ->setParameter('term', $term . '%')
            ->groupBy('aa.last_name', 'aa.first_name')
            ->setMaxResults($limit);

        $authorsArray = $qb->getQuery()->getArrayResult();

        $qbUsers->select("trim(concat(u.first_name, concat(' ', u.last_name))) as name")
            ->from('Newscoop\Entity\User', 'u')
            ->where($qb->expr()->like('u.last_name', ':term'))
            ->orWhere($qb->expr()->like('u.first_name', ':term'))
            ->setParameter('term', $term . '%')
            ->groupBy('u.last_name', 'u.first_name')
            ->setMaxResults($limit);

        $usersArray = $qbUsers->getQuery()->getArrayResult();

        return array_merge($authorsArray, $usersArray);
    }

    /**
     * Get author options
     *
     * @return array
     */
    public function getOptions()
    {
        $authors = array();
        foreach ($this->repository->findBy(array(), array('last_name' => 'asc', 'first_name' => 'asc')) as $author) {
            $authors[$author->getId()] = $author->getFullName();
        }

        return $authors;
    }
}
