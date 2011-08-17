<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\Repository\UserRepository;

/**
 * User service
 */
class UserService
{
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
                $this->currentUser = $this->em->getRepository('Newscoop\Entity\User')
                    ->find($this->auth->getIdentity());
            }
        }

        return $this->currentUser;
    }
}
