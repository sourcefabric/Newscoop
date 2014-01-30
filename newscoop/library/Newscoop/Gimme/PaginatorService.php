<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Gimme;

use Knp\Component\Pager\Paginator;
use Newscoop\Gimme\Pagination;
use Newscoop\Gimme\PartialResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Gimme Pagination service.
 */
class PaginatorService
{
    /**
     * Paginator class
     * @var Knp\Component\Pager\Paginator
     */
    private $paginator;

    /**
     * Pagination object with parsed data from request.
     * @var Newscoop\Gimme\Pagination
     */
    private $pagination;

    /**
     * PartialResponse object with parsed data from request.
     * @var Newscoop\Gimme\PartialResponse
     */
    private $partialResponse;

    /**
     * Router class
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * Extra data injected to response when result have more items than requested.
     * @var array
     */
    private $paginationData;

    /**
     * Used route name
     * @var string
     */
    private $route;

    /**
     * Used route params
     * @var array
     */
    private $routeParams = array();

    /**
     * Construct Paginator service object
     * @param Paginator $paginator Paginator object
     * @param Router    $router    Router object
     */
    public function __construct(Paginator $paginator, Router $router)
    {
        $this->paginator = $paginator;
        $this->router = $router;
    }

    /**
     * Set Pagination object
     * @param Pagination $pagination Pagination object
     */
    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;

        $this->routeParams['page'] = $this->pagination->getPage();
        $this->routeParams['sort'] = $this->pagination->getSort();
        $this->routeParams['items_per_page'] = $this->pagination->getItemsPerPage();

        return $this;
    }

    /**
     * Get Pagination object
     * @return Pagination Pagination object
     */
    public function getPagination() {
        return $this->pagination;
    }

    /**
     * Set PartialResponse object
     * @param PartialResponse $partialResponse PartialResponse object
     */
    public function setPartialResponse($partialResponse)
    {
        $this->partialResponse = $partialResponse;
    }

    /**
     * Get PartialResponse object
     * @return PartialResponse PartialResponse object
     */
    public function getPartialResponse()
    {
        return $this->partialResponse;
    }

    /**
     * Set used route
     * @param string $route Used route in request
     */
    public function setUsedRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Set parameters required by route generator for used route
     * @param array $params Route parameters
     */
    public function setUsedRouteParams(array $params = array())
    {
        $this->routeParams = array_merge(
            $this->routeParams,
            $params
        );

        return $this;
    }

    /**
     * Set pagination data from paginator
     * @param array $paginationData array with calculated pagination data
     */
    public function setPaginationData(array $paginationData)
    {
        $this->paginationData = array(
            'itemsPerPage' => $paginationData['numItemsPerPage'],
            'currentPage' => $paginationData['current'],
            'itemsCount' => $paginationData['totalCount']
        );

        $this->paginationData = array_merge(
            $this->paginationData, 
            $this->getPaginationLinks($paginationData)
        );

        return $this;
    }

    /**
     * Paginate data
     *
     * @param mixed $data   Data to paginate
     * @param array $params Prameters for Paginator
     *
     * @return array         Paginated data
     */
    public function paginate($data, $params = array())
    {
        $paginator = $this->paginator->paginate(
            $data,
            $this->pagination->getPage(),
            $this->pagination->getItemsPerPage(),
            $params
        );

        $items['items'] = $paginator->getItems();

        if (count($items['items']) == 0) {
            throw new NotFoundHttpException('Results was not found.');
        }

        /**
         * Set pagination object only when need
         */
        if ($this->paginationData['itemsPerPage'] < $this->paginationData['itemsCount']) {
            $items['pagination'] = $this->paginationData;
        }

        return $items;
    }

    /**
     * Generate links for pagination object
     *
     * @param array $paginationData Array with calculated pagination data
     *
     * @return array                 Array with links
     */
    private function getPaginationLinks($paginationData)
    {
        // idea is that if you are somewhere and you use pagination 
        // and get link to go back it should be the very same uri you've visited
        // in general it can filter all the params with default values

        $data = array();

        if ($paginationData['current'] < $paginationData['lastPageInRange']) {
            $this->routeParams['page'] = $paginationData['current'] + 1;
            $data['nextPageLink'] = $this->router->generate($this->route, $this->routeParams, true);
        }

        if ($paginationData['current'] > $paginationData['firstPageInRange']-1 && $paginationData['current'] > 1) {
            $this->routeParams['page'] = $paginationData['current'] - 1;
            $data['previousPageLink'] = $this->router->generate($this->route, $this->routeParams, true);
        }

        return $data;
    }
}