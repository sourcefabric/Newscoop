<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite urlparameters function plugin
 *
 * Type:     function
 * Name:     urlparameters
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $uriString
 *      The URL parameters requested
 */
function smarty_function_urlparameters($p_params, &$p_smarty)
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
	
    return $context->$url->url_parameters;
} // fn smarty_function_urlparameters

?>