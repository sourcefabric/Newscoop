<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite uri function plugin
 *
 * Type:     function
 * Name:     uri
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $uriString
 *      The requested URI
 */
function smarty_function_uri($p_params, &$p_smarty)
{
    $context = $p_smarty->get_template_vars('gimme');

    $url = 'url';
    if (isset($p_params['static_file']) && !empty($p_params['static_file'])) {
    	$p_params['options'] = 'static_file ' . $p_params['static_file'];
    } else {
    	$params = preg_split("/[\s]+/", $p_params['options']);
    	foreach ($params as $index=>$param) {
    		if (strcasecmp('fromstart', $param) == 0) {
    			$url = 'default_url';
    			unset($params[$index]);
    			$p_params['options'] = implode(', ', $params);
    			break;
    		}
    	}
    }

    // sets the URL parameter option
    $context->$url->uri_parameter = $p_params['options'];

    return $context->$url->uri;
} // fn smarty_function_uri

?>