<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Newscoop\Services\UserService;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Run authentication resolver on request
 */
class AuthenticationListener
{
    /**
     * User service
     *
     * @var UserService
     */
    protected $userService;

    /**
     * Contruct AuthenticationListener object
     *
     * @param UserService $userService   User service
     */
    public function __construct(UserService $userService, Router $router)
    {
        $this->userService = $userService;
        $this->router = $router;
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        if ($this->userService->getCurrentUser() === null) {
            return new RedirectResponse($this->router->generate('admin_logout'));
        }
    }
}
