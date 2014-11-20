<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList;

use Newscoop\TemplateList\PaginatedBaseList;

/**
 * Slideshows List
 */
class SlideshowItemsList extends PaginatedBaseList
{
    protected function prepareList($criteria, $parameters)
    {
        $em = \Zend_Registry::get('container')->get('em');
        $queryBuilder = $em->getRepository('Newscoop\Package\Item')
            ->getListByCriteria($criteria);
        $list = $this->paginateList($queryBuilder, null, $criteria->maxResults);

        $items = array();
        foreach ($list->items as $item) {
            $items[] = new \Newscoop\TemplateList\Meta\SlideshowItemMeta($item);
        }
        $list->items = $items;

        return $list;
    }

    protected function convertParameters($firstResult, $parameters)
    {
        parent::convertParameters($firstResult, $parameters);
    }
}
