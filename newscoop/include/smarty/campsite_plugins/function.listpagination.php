<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Render list pagination
 *
 * Type:     function
 * Name:     listpagination
 * Purpose:  Render nice pagination for current list
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_listpagination($params, &$smarty)
{
    $context = $smarty->getTemplateVars('gimme');

    if (!is_subclass_of($context->current_list, '\Newscoop\TemplateList\PaginatedBaseList')) {
        return;
    }

    if ($context->current_list->getIndex() === $context->current_list->getEnd()) {
        $templatesService = \Zend_Registry::get('container')->get('newscoop.templates.service');
        if (array_key_exists('file', $params)) {
            $context->current_list->pagination->renderer = function ($data) use ($templatesService, $params) {
                return $templatesService->fetchTemplate($params['file'], array('data' => $data));
            };
        } else {
            $context->current_list->pagination->renderer = function ($data) use ($templatesService, $params) {
                return $templatesService->fetchTemplate('_pagination/twitter_bootstrap_v2_pagination.tpl', array('data' => $data));
            };
        }

        return $context->current_list->pagination;
    }
}
