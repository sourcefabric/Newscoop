<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LinkRequestListener
{
    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;
    protected $urlMatcher;

    /**
     * @param ControllerResolverInterface $controllerResolver The 'controller_resolver' service
     * @param UrlMatcherInterface         $urlMatcher         The 'router' service
     */
    public function __construct(ControllerResolverInterface $controllerResolver, UrlMatcherInterface $urlMatcher)
    {
        $this->resolver = $controllerResolver;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->getRequest()->headers->has('link')) {
            return;
        }

        $links  = array();
        $header = $event->getRequest()->headers->get('link');

        /*
         * Due to limitations, multiple same-name headers are sent as comma
         * separated values.
         *
         * This breaks those headers into Link headers following the format
         * http://tools.ietf.org/html/rfc2068#section-19.6.2.4
         */
        while (preg_match('/^((?:[^"]|"[^"]*")*?),/', $header, $matches)) {
            $header  = trim(substr($header, strlen($matches[0])));
            $links[] = $matches[1];
        }

        if ($header) {
            $links[] = $header;
        }


        $requestMethod = $this->urlMatcher->getContext()->getMethod();

        // The controller resolver needs a request to resolve the controller.
        $stubRequest = new Request();

        foreach ($links as $idx => $link) {
            // Force the GET method to avoid the use of the
            // previous method (LINK/UNLINK)
            $this->urlMatcher->getContext()->setMethod('GET');

            $linkParams = explode(';', trim($link));
            $resourceType = null;
            if (count($linkParams) > 1) {
                $resourceType = trim(preg_replace('/<|>/', '', $linkParams[1]));
                $resourceType = str_replace("\"", "", str_replace("rel=", "", $resourceType));
            }
            $resource   = array_shift($linkParams);
            $resource   = preg_replace('/<|>/', '', $resource);
            $tempRequest = Request::create($resource);

            try {
                $route = $this->urlMatcher->match($tempRequest->getRequestUri());
            } catch (\Exception $e) {
                // If we don't have a matching route we return the original Link header
                continue;
            }

            if (strpos($route['_route'], 'newscoop_gimme_') === false) {
                return;
            }

            $stubRequest->attributes->replace($route);
            $stubRequest->server = $event->getRequest()->server;
            if (false === $controller = $this->resolver->getController($stubRequest)) {
                continue;
            }

            // Make sure @ParamConverter is handled
            $subEvent = new FilterControllerEvent($event->getKernel(), $controller, $stubRequest, HttpKernelInterface::MASTER_REQUEST);
            $kernelSubEvent = new GetResponseEvent($event->getKernel(), $stubRequest, HttpKernelInterface::MASTER_REQUEST);
            $event->getDispatcher()->dispatch(KernelEvents::REQUEST, $kernelSubEvent);
            $event->getDispatcher()->dispatch(KernelEvents::CONTROLLER, $subEvent);
            $controller = $subEvent->getController();

            $arguments = $this->resolver->getArguments($stubRequest, $controller);

            try {
                $result = call_user_func_array($controller, $arguments);
                // Our api returns objects for single resources
                if (!is_object($result)) {
                    continue;
                }
                $links[$idx] = array(
                    'object' => $result,
                    'resourceType' => $resourceType
                );
            } catch (\Exception $e) {
                $links[$idx] = array(
                    'object' => $e,
                    'resourceType' => 'exception'
                );

                continue;
            }
        }

        $event->getRequest()->attributes->set('links', $links);
        $this->urlMatcher->getContext()->setMethod($requestMethod);
    }
}
