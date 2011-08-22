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
 * User service
 */
class UserService
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
     * Create user
     *
     * @param array $values
     * @return Newscoop\Entity\User
     */
    public function create(array $values)
    {
        $user = new User();
        $this->em->getRepository(self::ENTITY)
            ->save($user, $values);
        $this->em->flush();
        return $user;
    }

    /**
     * Delete user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function delete(User $user)
    {
        if ($this->auth->getIdentity() == $user->getId()) {
            throw new \InvalidArgumentException("You can't delete yourself");
        }

        $user->setStatus(User::STATUS_DELETED);
        $this->em->flush();
    }
}
