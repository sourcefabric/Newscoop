<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User;

/**
 * User repository.
 */
class UserRepository extends EntityRepository
{
    /**
     * Save user.
     *
     * @param Newscoop\Entity\User $user
     * @param array $values
     * @return void
     */
    public function save(User $user, array $values = array())
    {
        $user->setUsername($values['username']);
        $user->setPassword($values['password']);
        $user->setFirstName($values['first_name']);
        $user->setLastName($values['last_name']);
        $user->setEmail($values['email']);
        $user->setStatus($values['status']);

        $this->getEntityManager()->persist($user);
    }

    /**
     * Delete user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function delete(User $user)
    {
        $this->getEntityManager()->remove($user);
    }
}
