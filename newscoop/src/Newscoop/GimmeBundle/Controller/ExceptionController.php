<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use Newscoop\GimmeBundle\Util\ExceptionWrapper;
use FOS\RestBundle\Controller\ExceptionController as FOSExceptionController;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends FOSExceptionController
{
   /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   Request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $format    The format to use for rendering (html, xml, ...)
     *
     * @return Response Response instance
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $urlMatcher = $this->container->get('router');
        try {
            $context = new \Symfony\Component\Routing\RequestContext($request->getPathInfo(), $request->getMethod());
            $urlMatcher->setContext($context);
            $match = $urlMatcher->match($context->getBaseUrl());
        } catch (\Exception $e) {
            return;
        }

        if (strpos($match['_route'], 'newscoop_gimme_') === false) {
            return;
        }

        return parent::showAction($request, $exception, $logger, $format);
    }

    protected function createExceptionWrapper(array $parameters)
    {
        return $parameters;
    }

    protected function getParameters(ViewHandler $viewHandler, $currentContent, $code, FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $defaultParameters = parent::getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);
        $parameters = array(
            'errors' => array(
                array(
                    'code' => $defaultParameters['status_code'],
                    'message' => $defaultParameters['message'],
                )
            )
        );

        return $parameters;
    }

    /**
     * Determine the format to use for the response
     *
     * @param Request $request Request instance
     * @param string  $format  The format to use for rendering (html, xml, ...)
     *
     * @return string Encoding format
     */
    protected function getFormat(Request $request, $format)
    {
        return 'json';
    }
}
