<?php

/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
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
    private $pageParameterName;

    private $pageNumber;

    private $paginatorService;

    public function __construct($criteria, $paginatorService)
    {
        $this->paginatorService = $paginatorService;

        parent::__construct($criteria);
    }

    public function getPaginator()
    {
        return $this->paginatorService->getPaginator();
    }

    protected function paginateList($target, $pageNumber, $maxResults, $list = null)
    {
        if (!$list) {
            $list = new ListResult();
        }

        if (!$pageNumber) {
            $pageNumber = $this->pageNumber;
        }

        $this->pagination = $this->paginatorService->paginate($target, $pageNumber, $maxResults);
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
