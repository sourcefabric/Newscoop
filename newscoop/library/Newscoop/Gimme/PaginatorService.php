<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Gimme;

/**
 * Gimme Pagination object.
 */
class PaginatorService {
    private $paginator;
    /**
     * Pagination object with parsed data from request.
     */
    private $pagination;
    private $router;
    private $distinct = true;
    private $paginatorData;
    private $route;
    private $routeParams = array();

    public function __construct($paginator, $router)
    {
        $this->paginator = $paginator;
        $this->router = $router;
    }

    public function setPagination($pagination)
    {
        $this->pagination = $pagination;

        $this->routeParams['page'] = $this->pagination->getPage();
        $this->routeParams['sort'] = $this->pagination->getSort();
        $this->routeParams['items_per_page'] = $this->pagination->getItemsPerPage();

        return $this;
    }

    public function setDistinct($distinct)
    {
        $this->distinct = $distinct;

        return $this;
    }

    public function setUsedRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function setUsedRouteParams(array $params = array())
    {
        $this->routeParams = $params;

        return $this;
    }

    public function getPaginationLinks($paginatorData)
    {
        $data = array();

        if ($paginatorData['current'] < $paginatorData['lastPageInRange']-1) {
            $this->routeParams['page'] = $paginatorData['current'] + 1;
            $data['nextPageLink'] = $this->router->generate($this->route, $this->routeParams, true);
        }

        if ($paginatorData['current'] > $paginatorData['firstPageInRange']-1) {
            $this->routeParams['page'] = $paginatorData['current'] - 1;
            $data['previousPageLink'] = $this->router->generate($this->route, $this->routeParams, true);
        }

        return $data;
    }

    public function setPaginationData($paginatorData)
    {
        $this->paginatorData = array(
            'itemsPerPage' => $paginatorData['numItemsPerPage'],
            'currentPage' => $paginatorData['current'],
            'itemsCount' => $paginatorData['totalCount']
        );

        $this->paginatorData = array_merge(
            $this->paginatorData, 
            $this->getPaginationLinks($paginatorData)
        );

        return $this;
    }

    public function paginate($data)
    {
        $paginator = $this->paginator->paginate(
            $data, 
            $this->pagination->getPage(), 
            $this->pagination->getItemsPerPage(), 
            array(
                'distinct' => $this->distinct
            )
        );

        /**
         * Set pagination object
         */
        if ($this->paginatorData['itemsPerPage'] < $this->paginatorData['itemsCount']) {
            $items['pagination'] = $this->paginatorData;
        }

        $items['items'] = $paginator->getItems();

        return $items;
    }
}