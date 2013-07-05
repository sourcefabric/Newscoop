<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Simple sccess voter on request
 */
class AccessListener
{   
    /**
     * Zend Framework Router
     * @var zendRouter
     */
    private $zendRouter;

    /**
     * Newscoop User Service
     * @var zendRouter
     */
    private $userService;

    /**
     * Contruct AccessListener object
     * @param $zendRouter Zend Framework Router service
     * @param $userService Newscoop User Service
     */
    public function __construct($zendRouter, $userService)
    {
        $this->zendRouter = $zendRouter;
        $this->userService = $userService;
    }
    
    public function onRequest(GetResponseEvent $event)
    {
        $pos = strpos($_SERVER['REQUEST_URI'], '/admin');
        if ($pos !== false) {
            $auth = \Zend_Auth::getInstance();
            if(!$auth->hasIdentity()) {
                $event->setResponse(new RedirectResponse($this->zendRouter->assemble(array(
                    'controller' => null,
                    'action' => 'login.php',
                    'module' => 'admin'
                ), 'default')));
                return;
            } else {
                $user = $this->userService->getCurrentUser();

                if (!$user->isAdmin()) { // can't go into admin
                    $event->setResponse(new RedirectResponse($this->zendRouter->assemble(array(
                        'controller' => 'index',
                        'action' => 'index',
                    ), 'default')));
                }
            }
        }
    }
}