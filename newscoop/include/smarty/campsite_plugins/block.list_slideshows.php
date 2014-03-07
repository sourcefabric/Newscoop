<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Newscoop list_slideshows block plugin
 *
 * Type:     block
 * Name:     list_slideshows
 *
 * @param array   $params
 * @param mixed   $content
 * @param object  $smarty
 * @param boolean $repeat
 *
 * @return string
 */
function smarty_block_list_slideshows($params, $content, &$smarty, &$repeat)
{
    $context = $smarty->getTemplateVars('gimme');
    $paginatorService = \Zend_Registry::get('container')->get('newscoop.listpaginator.service');
    $cacheService = \Zend_Registry::get('container')->get('newscoop.cache');

    if (!isset($content)) { // init
        $start = $context->next_list_start('Newscoop\TemplateList\SlideshowsList');
        $list = new \Newscoop\TemplateList\SlideshowsList(
            new \Newscoop\Criteria\SlideshowCriteria(),
            $paginatorService,
            $cacheService
        );

        $list->setPageParameterName($context->next_list_id($context->getListName($list)));
        $list->setPageNumber(\Zend_Registry::get('container')->get('request')->get($list->getPageParameterName(), 1));

        $list->getList(0, $params);
        if ($list->isEmpty()) {
            $context->setCurrentList($list, array());
            $context->resetCurrentList();
            $repeat = false;

            return null;
        }

        $context->setCurrentList($list, array('slideshow', 'pagination'));
        $context->slideshow = $context->current_slideshows_list->current;
        $repeat = true;
    } else { // next
        $context->current_slideshows_list->defaultIterator()->next();
        if (!is_null($context->current_slideshows_list->current)) {
            $context->slideshow = $context->current_slideshows_list->current;
            $repeat = true;
        } else {
            $context->resetCurrentList();
            $repeat = false;
        }
    }

    return $content;
}
