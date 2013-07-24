<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
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
        $pos = strpos($request->server->get('REQUEST_URI'), '/api');

        if ($pos === false) {
            return;
        }

        $responseData = $event->getResponse()->getContent();
        $formatedJson = Json::indent($responseData);
        $newResponse = new Response($formatedJson);
        $event->setResponse($newResponse);
    }
}