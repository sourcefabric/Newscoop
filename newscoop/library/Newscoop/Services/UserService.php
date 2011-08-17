<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Repository\UserRepository;

/**
 * User service
 */
class UserService
{
    /** @var Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    /** @var Zend_Auth */
    protected $auth;

    /** @var Newscoop\Entity\User */
    protected $currentUser;

    /**
     * @param Newscoop\Entity\Repository\UserRepository $repository
     * @param Zend_Auth $auth
     */
    public function __construct($repository, \Zend_Auth $auth)
    {
        $this->repository = $repository;
        $this->auth = $auth;
    }

    /**
     * Get current user.
     *
     * @return Newscoop\Entity\User
     */
    public function getCurrentUser()
    {
        if ($this->currentUser === NULL) {
            if ($this->auth->hasIdentity()) {
                $this->currentUser = $this->repository->find($this->auth->getIdentity());
            }
        }

        return $this->currentUser;
    }
}
