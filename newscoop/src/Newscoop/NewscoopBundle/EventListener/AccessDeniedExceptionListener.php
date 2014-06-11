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
use Symfony\Component\Translation\Translator;

class AccessDeniedExceptionListener
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Symfony\Component\Templating
     */
    private $templatingService;

    /**
     * Construct
     * @param Translator $translator
     * @param Templating $templatingService
     */
    public function __construct(Translator $translator, $templatingService)
    {
        $this->translator = $translator;
        $this->templatingService = $templatingService;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = $this->translator->trans('nopermissions');

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
