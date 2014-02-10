<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Get parameter from symfony Request object
 *
 * usage:
 *         {{ get_request_param assign="request" }} - get request object
 *         {{ $request }} - request object is under $request smarty variable
 *
 *         {{ get_request_param name="param name" default="null" }} - get single parameter value from post and get
 *
 * Type:     function
 * Name:     get_request_param
 * Purpose:  Get parameters from request
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_get_request_param($params, &$smarty)
{
    $request = \Zend_Registry::get('container')->get('request');
    $request = $request::createFromGlobals();

    if (count($params) == 0) {
        return $request;
    }

    if (array_key_exists('assign', $params)) {
        $smarty->assign($params['assign'], $request);
    }

    if (!array_key_exists('name', $params) && !array_key_exists('assign', $params)) {
        throw new \Newscoop\NewscoopException('Parameter "name" is required');
    }

    $default = array_key_exists('default', $params)? $params['default'] : '';

    return $request->get($params['name'], $default);
}
