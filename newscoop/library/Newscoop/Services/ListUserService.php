<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\User\UserCriteria;

/**
 * List User service
 */
class ListUserService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var array */
    private $config = array();

    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(array $config, EntityManager $em)
    {
        $this->config = $config;
        $this->em = $em;
    }

    /**
     * Find by criteria
     *
     * @param Newscoop\User\UserCriteria $criteria
     * @return Newscoop\ListResult;
     */
    public function findByCriteria(UserCriteria $criteria)
    {
        return $this->getRepository()->getListByCriteria($criteria);
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

    /**
     * List active users
     *
     * @return array
     */
    public function getActiveUsers($countOnly=false, $page=1, $limit=8, $editors = array())
    {   
        $offset = ($page-1) * $limit;
        $result = $this->getRepository()->findActiveUsers($countOnly, $offset, $limit, $editors);

        if ($countOnly) {
            return $result;
        }

        return $result;
    }

    /**
     * Get random list of users
     *
     * @param int $limit
     * @return array
     */
    public function getRandomList($limit = 25)
    {
        return $this->getRepository()->getRandomList($limit);
    }

    /**
     * List users by first letter
     *
     * @return array
     */
    public function findUsersLastNameInRange($letters, $countOnly=false, $page=1, $limit=25)
    {
        $offset = ($page-1) * $limit;
        $result = $this->getRepository()->findUsersLastNameInRange($letters, $countOnly, $offset, $limit);

        if($countOnly) {
            return $result[1];
        }

        return $result;
    }

    /**
     * Find user by string
     *
     * @return array
     */
    public function findUsersBySearch($search, $countOnly=false, $page=1, $limit=25)
    {
        $offset = ($page-1) * $limit;

        $result = $this->getRepository()->searchUsers($search, $countOnly, $offset, $limit);

        if($countOnly) {
            return $result[1];
        }

        return $result;
    }

    /**
     * List editors
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findEditors($limit = NULL, $offset = NULL)
    {
        return $this->getRepository()->findEditors($this->config['blog']['role'], $limit, $offset);
    }

    /**
     * Get editors count
     *
     * @return int
     */
    public function getEditorsCount()
    {
        return $this->getRepository()->getEditorsCount($this->config['blog']['role']);
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
