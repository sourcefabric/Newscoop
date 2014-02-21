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
    private $paginator;

    private $templatesService;

    /**
     * Set page partameter name
     *
     * @param string $pageParameterName
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
     */
    public function setRouteParams(array $params = array())
    {
        $this->paginator->setDefaultPaginatorOptions(array(
            'route_params' => $params
        ));

        return $this;
    }

    public function __construct($templatesService)
    {
        $this->paginator = new Paginator();
        $this->templatesService = $templatesService;
    }

    public function paginate($target, $pageNumber = 1, $limit = 10)
    {
        $pagination = $this->paginator->paginate($target, $pageNumber, $limit);
        $pagination->renderer = function ($data) {
            return $this->templatesService->fetchTemplate('_pagination/twitter_bootstrap_v2_pagination.tpl', array('data' => $data));
        };

        return $pagination;
    }

    /**
     * Get paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}
