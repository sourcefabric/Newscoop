<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\User;

/**
 * List User service
 */
class ListUserService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find by given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = array(), $limit = NULL, $offset = NULL)
    {
        return $this->getRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Count by given criteria
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria = array())
    {
        return $this->getRepository()->countBy($criteria);
    }

    /**
     * Find one user by criteria
     *
     * @param array $criteria
     * @return Newscoop\Entity\User
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function getPublicUsers()
    {
        $users = $this->findBy(array(
            'is_public' => true,
            'status' => User::STATUS_ACTIVE,
        ), array('id' => 'desc'));

        return $users;
    }

    public function findUsersLastNameInRange($letters)
    {
        return $this->getRepository()->findUsersLastNameInRange($letters);
    }

    public function findUsersBySearch($search)
    {
        return $this->getRepository()->searchUsers($search);
    }

    /**
     * Get repository
     *
     * @return Newscoop\Entity\Repository\UserRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\User');
    }
}
