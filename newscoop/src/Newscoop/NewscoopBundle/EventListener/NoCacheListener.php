<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * NoCache cookie listener.
 */
class NoCacheListener
{
    public function onResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        if ($session->has('NO_CACHE')) {
            if ($session->get('NO_CACHE') == true) {
                $response = $event->getResponse();
                $response->headers->setCookie(new Cookie('NO_CACHE', 1, 0, '/'));
                $event->setResponse($response);
            } elseif ($session->get('NO_CACHE') == false) {
                $response = $event->getResponse();
                $response->headers->clearCookie('NO_CACHE', '/');
                $event->setResponse($response);
            }
        }
    }
}
