<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccessDeniedExceptionListener
{
    private $container;

    /**
     * Construct
     * @param Service Container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof AccessDeniedHttpException) {
            $request = $event->getRequest();
            $response = new JsonResponse(array('message' => $exception->getMessage()));

            if ($request->getRequestFormat() === 'html') {
                $response = $this->container->get('templating')->renderResponse('NewscoopNewscoopBundle:Exception:exception.html.twig', array(
                    'message' => $exception->getMessage()
                ));
            }

            $event->setResponse($response);
        }
    }
}
