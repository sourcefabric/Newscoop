<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Newscoop\Gimme\Pagination;
use Newscoop\Gimme\PartialResponse;
use Newscoop\Gimme\PaginatorService;

/**
 * Get parameters from request and set Pagination and PartialResponse objects to session
 */
class PaginationListener
{
    /**
     * Session object
     * @var Session
     */
    protected $session;

    /**
     * Paginator service object
     * @var PaginatorService
     */
    protected $paginatorService;

    /**
     * Construct PaginationListener object
     * @param PaginatorService $paginatorService PaginationService object
     */
    public function __construct(PaginatorService $paginatorService)
    {
        $this->paginatorService = $paginatorService;
    }

    /**
     * Get data from request and create Pagination and PartialResponse events.
     * @param  GetResponseEvent $event Event object with request
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $pagination = new Pagination();
        $partialResponse = new PartialResponse();

        if ($request->query->has('page')) {
            $pagination->setPage($request->query->get('page'));
        }

        if ($request->query->has('sort')) {
            $pagination->setSort($request->query->get('sort'));
        }

        if ($request->query->has('items_per_page')) {
            $pagination->setItemsPerPage($request->query->get('items_per_page'));
        }

        if ($request->query->has('fields')) {
            $partialResponse->setFields($request->query->get('fields'));
        }

        $this->paginatorService->setPagination($pagination);
        $this->paginatorService->setPartialResponse($partialResponse);

        /**
         * Append all used parameters from get and post
         */
        $this->paginatorService->setUsedRouteParams(array_merge(
            $request->query->all(),
            $request->request->all()
        ));
    }
}