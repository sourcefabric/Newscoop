<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\Entity\UserToken;

/**
 * User service
 */
class UserTokenService
{
    const TOKEN_LENGTH = 40;
    const TOKEN_LIFETIME = 'P2D'; // DateInterval syntax

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
     * Generate user action token
     *
     * @param Newscoop\Entity\User $user
     * @param string $action
     * @return string
     */
    public function generateToken(User $user, $action = 'any')
    {
        $token = $user->generateRandomString(self::TOKEN_LENGTH);
        $this->em->persist(new UserToken($user, $action, $token));
        $this->em->flush();
        return $token;
    }

    /**
     * Check user action token
     *
     * @param Newscoop\Entity\User $user
     * @param string $token
     * @param string $action
     * @return bool
     */
    public function checkToken(User $user, $token, $action = 'any')
    {
        $userToken = $this->em->find('Newscoop\Entity\UserToken', array(
            'user' => $user->getId(),
            'action' => $action,
            'token' => $token,
        ));

        if (empty($userToken)) {
            return false;
        }

        $now = new \DateTime();
        return $now->sub(new \DateInterval(self::TOKEN_LIFETIME))->getTimestamp() < $userToken->getCreated()->getTimestamp();
    }

    /**
     * Invalidate token
     *
     * @param Newscoop\Entity\User $user
     * @param string $action
     * @return void
     */
    public function invalidateTokens(User $user, $action = 'any')
    {
        $tokens = $this->em->getRepository('Newscoop\Entity\UserToken')->findBy(array(
            'user' => $user->getId(),
            'action' => $action,
        ));

        foreach ($tokens as $token) {
            $this->em->remove($token);
        }

        $this->em->flush();
    }
}
