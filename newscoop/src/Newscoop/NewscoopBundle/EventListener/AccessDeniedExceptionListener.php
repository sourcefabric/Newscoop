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
    /**
     * @var Symfony\Component\Templating
     */
    private $templatingService;

    /**
     * Constructor
     */
    public function __construct($templatingService)
    {
        $this->templatingService = $templatingService;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = $exception->getMessage();

        if ($exception instanceof AccessDeniedHttpException) {
            $request = $event->getRequest();
            $response = new JsonResponse(array('message' => $message));

            if ($request->getRequestFormat() === 'html') {
                $response = $this->templatingService->renderResponse('NewscoopNewscoopBundle:Exception:exception.html.twig', array(
                    'message' => $message
                ));
            }

            $event->setResponse($response);
        }
    }
}
