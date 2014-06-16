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
     * @param  string $term      Search term
     * @param  int    $limit     Max results
     * @param  bool   $alsoUsers Also return users
     *
     * @return array
     */
    public function getAuthors($term = null, $limit = null, $alsoUsers = false)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select("trim(concat(aa.first_name, concat(' ', aa.last_name))) as name")
            ->from('Newscoop\Entity\Author', 'aa');

        if ($term !== null && trim($term) !== '') {
            $qb
            ->where($qb->expr()->like('aa.last_name', ':term'))
            ->orWhere($qb->expr()->like('aa.first_name', ':term'))
            ->setParameter('term', $term . '%')
            ->groupBy('aa.last_name', 'aa.first_name');
        }

        if (!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        $authorsArray = $qb->getQuery()->getArrayResult();

        if ($alsoUsers) {
            $qbUsers = clone $qb;
            $qbUsers->resetDQLPart('from');
            $qbUsers->from('Newscoop\Entity\User', 'aa');
            $usersArray = $qbUsers->getQuery()->getArrayResult();
            $authorsArray = array_merge($authorsArray, $usersArray);
        }

        return $authorsArray;
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
