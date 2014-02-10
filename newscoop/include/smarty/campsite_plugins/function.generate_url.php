<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Generate url for symfony route
 *
 * Type:     function
 * Name:     generate_url
 * Purpose:  Get url for symfony route
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_generate_url($params, &$smarty)
{
    $router = \Zend_Registry::get('container')->get('router');

    $acceptedParameters = array('route', 'parameters', 'absolute');
    $route = null;
    $parameters = array();
    $absolute = false;

    foreach ($params as $key => $value) {
        if (in_array($key, $acceptedParameters)) {
            if ($key == 'route') {
                $route = $value;
            } else if ($key == 'parameters' && is_array($value)) {
                $parameters = $value;
            } else if ($key == 'absolute') {
                $absolute = $value;
            }
        }
    }

    return $router->generate($route, $parameters, $absolute);
}
