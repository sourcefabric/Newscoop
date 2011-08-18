<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Datatable\ISource as DatatableSource;


/**
 * User service
 */
class UserService implements DatatableSource
{
    const ENTITY = 'Newscoop\Entity\User';

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Zend_Auth */
    protected $auth;

    /** @var Newscoop\Entity\User */
    protected $currentUser;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Zend_Auth $auth
     */
    public function __construct(EntityManager $em, \Zend_Auth $auth)
    {
        $this->em = $em;
        $this->auth = $auth;
    }

    /**
     * Get current user
     *
     * @return Newscoop\Entity\User
     */
    public function getCurrentUser()
    {
        if ($this->currentUser === NULL) {
            if ($this->auth->hasIdentity()) {
                $this->currentUser = $this->em->getRepository(self::ENTITY)
                    ->find($this->auth->getIdentity());
            }
        }

        return $this->currentUser;
    }

    /**
     * Find user
     *
     * @param int $id
     * @return Newscoop\Entity\User
     */
    public function find($id)
    {
        return $this->em->getRepository(self::ENTITY)
            ->find($id);
    }

    /**
     * Find all users
     *
     * @return array
     */
    public function findAll()
    {
        return $this->em->getRepository(self::ENTITY)
            ->findAll();
    }

    /**
     * Find by given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     */
    public function findBy($critearia, array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return $this->em->getRepository(self::ENTITY)
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get data for datatable
     *
     * @param array $params
     * @param array $cols
     * @return array
     */
    public function getData(array $params, array $cols)
    {
    }

    /**
     * Get data count for datatable
     *
     * @param array $params
     * @param array $cols
     * @return int
     */
    public function getCount(array $params, array $cols)
    {
    }
}
