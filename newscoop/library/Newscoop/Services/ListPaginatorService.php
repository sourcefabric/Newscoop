<?php

/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Knp\Component\Pager\Paginator;

/**
 * List Paginator service
 */
class ListPaginatorService
{
    /**
     * @var \Knp\Component\Pager\Paginator
     */
    protected $paginator;


    /**
     * Set page partameter name
     *
     * @param string $pageParameterName
     *
     * @return self
     */
    public function setPageParameterName($pageParameterName)
    {
        $this->paginator->setDefaultPaginatorOptions(array(
            'pageParameterName' => $pageParameterName,
        ));

        return $this;
    }

    /**
     * Set used route
     *
     * @param string $route Used route in request
     *
     * @return self
     */
    public function setRoute($route)
    {
        $this->paginator->setDefaultPaginatorOptions(array(
            'route' => $route
        ));

        return $this;
    }

    /**
     * Set parameters required by route generator for used route
     *
     * @param array $params Route parameters
     *
     * @return self
     */
    public function setRouteParams(array $params = array())
    {
        $this->paginator->setDefaultPaginatorOptions(array(
            'route_params' => $params
        ));

        return $this;
    }

    public function __construct()
    {
        $this->paginator = new Paginator();
    }

    /**
     * Paginate target with passed page and limit, apply default pagination remplate to renderer
     *
     * @param mixed   $target
     * @param integer $pageNumber
     * @param integer $limit
     *
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function paginate($target, $pageNumber = 1, $limit = 10)
    {
        $pagination = $this->paginator->paginate($target, $pageNumber, $limit);

        return $pagination;
    }

    /**
     * Get paginator
     *
     * @return \Knp\Component\Pager\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}
