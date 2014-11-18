<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Add Access-Control-Allow-Origin header to response.
 */
class AllowOriginListener
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // HACK: revert exception status code to main request (i have no idea why it's chnaged to 500)
        if (is_array($content = json_decode($response->getContent(), true))) {
            if (array_key_exists('errors', $content)) {
                $response->setStatusCode($content['errors'][0]['code'], $content['errors'][0]['message']);
            }
        }

        if (!$this->container->hasParameter('newscoop.gimme.allow_origin')) {
            return false;
        }

        $allowedHosts = $this->container->getParameter('newscoop.gimme.allow_origin');

        if (count($allowedHosts) == 0) {
            return false;
        }

        $allowedMethods = array('POST', 'GET', 'PUT', 'DELETE', 'LINK', 'UNLINK', 'PATCH', 'OPTIONS');
        if (preg_match('/Firefox/', $request->headers->get('user-agent'))) {
            foreach ($allowedMethods as $method) {
                $allowedMethods[] = ucfirst(strtolower($method));
            }
        }
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));

        $response->headers->set('Access-Control-Expose-Headers', 'X-Location, X-Debug');

        if (in_array('*', $allowedHosts)) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } else {
            foreach ($allowedHosts as $host) {
                if ($request->server->get('HTTP_ORIGIN') == $host) {
                    $response->headers->set('Access-Control-Allow-Origin', $host);
                }
            }
        }

        $event->setResponse($response);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = new Response();

        if ($request->getMethod() == 'OPTIONS') {
            $response->headers->set('Access-Control-Allow-Headers', $request->headers->get('Access-Control-Request-Headers'));
            $event->setResponse($response);
        }
    }
}
