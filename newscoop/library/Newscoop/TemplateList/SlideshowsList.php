<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList;

use Newscoop\ListResult;
use Newscoop\TemplateList\PaginatedBaseList;

/**
 * Slideshows List
 */
class SlideshowsList extends PaginatedBaseList
{
    protected function prepareList($criteria, $parameters)
    {
        $em = \Zend_Registry::get('container')->get('em');
        $queryBuilder = $em->getRepository('Newscoop\Package\Package')
            ->getListByCriteria($criteria);
        $list = $this->paginateList($queryBuilder, null, $criteria->maxResults, false);

        $slideshows = array();
        foreach ($list->items as $slideshow) {
            $slideshows[] = new \Newscoop\TemplateList\Meta\SlideshowsMeta($slideshow);
        }
        $list->items = $slideshows;

        return $list;
    }

    protected function convertParameters($firstResult, $parameters)
    {
        parent::convertParameters($firstResult, $parameters);
    }
}
