<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\User;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User service
 */
class UserManager implements UserProviderInterface
{
    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        return $this->em->getRepository('Newscoop\Entity\User')->findOneBy(array(
            'username' => $username,
        ));
    }

    public function refreshUser(UserInterface $user)
    {
        return true;
    }

    public function supportsClass($class) {
        if ($class === 'Newscoop\Entity\User') {
            return true;
        }

        return false;
    }
}
