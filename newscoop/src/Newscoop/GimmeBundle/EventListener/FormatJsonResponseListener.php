<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Newscoop\Gimme\Json;

/**
 * Preetify json response.
 */
class FormatJsonResponseListener
{
    public function onResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if (strpos($route, 'newscoop_gimme_') === false) {
            return;
        }

        $response = $event->getResponse();
        $responseData = $event->getResponse()->getContent();
        $response->setContent(Json::indent($responseData));
        $event->setResponse($response);
    }
}
