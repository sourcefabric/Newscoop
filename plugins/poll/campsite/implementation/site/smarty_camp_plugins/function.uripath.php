<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite uripath function plugin
 *
 * Type: function
 * Name: uripath
 * Purpose:
 *
 * @param array $p_params
 * @param object $p_smarty
 *      The Smarty object
 *
 * @return string $uriString
 *      The URI path
 */
function smarty_function_uripath($p_params, &$p_smarty)
{
    $context = $p_smarty->get_template_vars('campsite');

    $url = 'url';
    $params = preg_split("/[\s]+/", $p_params['options']);
    foreach ($params as $index=>$param) {
        if (strcasecmp('fromstart', $param) == 0) {
            $url = 'default_url';
            unset($params[$index]);
            $p_params['options'] = implode(', ', $params);
            break;
        }
    }

    // sets the URL parameter option
    $context->$url->uri_parameter = $p_params['options'];

    return $context->$url->uri_path;
} // fn smarty_function_uripath

?>