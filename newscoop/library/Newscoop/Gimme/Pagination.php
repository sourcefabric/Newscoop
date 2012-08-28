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

    private $page = 0;
    private $sort = array();
    private $itemsPerPage = 10;

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setSort($sort)
    {
        if (count($sort) > 0) {
            $this->sort = $sort;
        }
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
}