<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\User;

use InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Newscoop\Services\UserService;
use Newscoop\Services\EmailService;

/**
 * Register user service
 */
class RegisterUserService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Newscoop\Services\UserService
     */
    private $userService;

    /**
     * @var Newscoop\Services\EmailService
     */
    private $emailService;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Services\UserService $userService
     * @param Newscoop\Services\EmailService $emailService
     */
    public function __construct(EntityManager $em, UserService $userService, EmailService $emailService)
    {
        $this->em = $em;
        $this->userService = $userService;
        $this->emailService = $emailService;
    }

    /**
     * Register user
     *
     * @param Newscoop\User\RegisterUserCommand $command
     * @return void
     */
    public function register(RegisterUserCommand $command)
    {
        if (empty($command->email)) {
            throw new InvalidArgumentException("Email must be set");
        }

        $user = $this->em->getRepository('Newscoop\Entity\User')
            ->findOneBy(array('email' => $command->email));

        if ($user === null) {
            $user = $this->userService->createPending($command->email);
        }

        if (!$user->isPending()) {
            throw new InvalidArgumentException("Email is used already");
        }

        $this->emailService->sendConfirmationToken($user);
    }
}
