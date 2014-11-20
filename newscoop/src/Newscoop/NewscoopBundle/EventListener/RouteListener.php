<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Add route and route params to listPaginator.
 */
class RouteListener
{
    protected $listPaginatorService;

    /**
     * @param \Newscoop\Services\ListPaginatorService $listPaginatorService
     */
    public function __construct($listPaginatorService)
    {
        $this->listPaginatorService = $listPaginatorService;
    }

    /**
     * Fill listPaginatorService with information about route and parameters from request
     *
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $params = array_merge($request->query->all(), $request->attributes->all());
        foreach ($params as $key => $param) {
            if (substr($key, 0, 1) == '_') {
                unset($params[$key]);
            }
        }

        $this->listPaginatorService->setRouteParams($params);
        $this->listPaginatorService->setRoute($request->attributes->get('_route'));
    }
}
