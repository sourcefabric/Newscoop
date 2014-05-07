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
class Pagination {

    /**
     * Requested page number
     * @var integer
     */
    protected $page = 1;

    /**
     * Array with sorting parameters
     * @var array
     */
    protected $sort = array();
    
    /**
     * Requested number items per page.
     * @var integer
     */
    protected $itemsPerPage = 10;

    /**
     * Set currently requested page number
     * @param integer $page Page number
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Get currently requested page number
     * @return integer Page number
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set sort parameters
     * @param array $sort Sort parameters array
     */
    public function setSort($sort)
    {
        if (count($sort) > 0) {
            $this->sort = $sort;
        }
    }

    /**
     * Get sort parameters
     * @return array Sort parameters array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set requested number items per page.
     * @param integer $itemsPerPage Number items per page.
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Get requested number items per page
     * @return integer Number items per page.
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
}