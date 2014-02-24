<?php

/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList;

use Newscoop\Criteria;
use Newscoop\ListResult;
use Newscoop\TemplateList\BaseList;

/**
 * Paginated Base List
 */
abstract class PaginatedBaseList extends BaseList
{
    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var int
     */
    private $pageNumber = 1;

    /**
     * @var \Newscoop\Services\TemplatesService
     */
    private $paginatorService;

    /**
     * @var \Newscoop\Services\CacheService
     */
    private $cacheService;

    /**
     * @param \Newscoop\Criteria                  $criteria
     * @param \Newscoop\Services\TemplatesService $paginatorService
     * @param \Newscoop\Services\CacheService     $cacheService
     */
    public function __construct($criteria, $paginatorService, $cacheService)
    {
        $this->paginatorService = $paginatorService;
        $this->cacheService = $cacheService;

        parent::__construct($criteria);
    }

    /**
     * Get Paginator instance from PaginatorService
     *
     * @return \Knp\Component\Pager\Paginator
     */
    public function getPaginator()
    {
        return $this->paginatorService->getPaginator();
    }

    /**
     * Paginate target and fill list items
     *
     * @param mixed      $target
     * @param int        $pageNumber
     * @param int        $maxResults
     * @param ListResult $list
     *
     * @return ListResult
     */
    protected function paginateList($target, $pageNumber, $maxResults, $list = null)
    {
        if (!$list) {
            $list = new ListResult();
        }

        if (!$pageNumber) {
            $pageNumber = $this->pageNumber;
        }

        $cacheId = array($this->getCacheKey(), $this->getPageNumber());

        if ($this->cacheService->contains($cacheId)) {
            $this->pagination = $this->cacheService->fetch($cacheId);
        } else {
            $this->pagination = $this->paginatorService->paginate($target, $pageNumber, $maxResults);
            $this->cacheService->save($cacheId, $this->pagination);
        }

        $list->count = $this->pagination->getTotalItemCount();
        $list->items = $this->pagination->getItems();

        return $list;
    }

    /**
     * Gets the value of pageParameterName.
     *
     * @return mixed
     */
    public function getPageParameterName()
    {
        return $this->pageParameterName;
    }

    /**
     * Sets the value of pageParameterName.
     *
     * @param mixed $pageParameterName the page parameter name
     *
     * @return self
     */
    public function setPageParameterName($pageParameterName)
    {
        $this->pageParameterName = $pageParameterName;
        $this->paginatorService->setPageParameterName($this->pageParameterName);

        return $this;
    }

    /**
     * Gets the value of pageNumber.
     *
     * @return mixed
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * Sets the value of pageNumber.
     *
     * @param mixed $pageNumber the page number
     *
     * @return self
     */
    public function setPageNumber($pageNumber = 1)
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }
}
