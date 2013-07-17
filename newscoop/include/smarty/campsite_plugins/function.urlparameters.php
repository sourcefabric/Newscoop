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
function smarty_function_urlparameters($p_params = array(), &$p_smarty)
{
    $context = $p_smarty->getTemplateVars('gimme');

    if (!array_key_exists('options', $p_params)) {
        $p_params['options'] = '';
    }

    $url = 'url';
    $p_params = preg_split("/[\s]+/", $p_params['options']);
    foreach ($p_params as $index=>$param) {
        if (strcasecmp('fromstart', $param) == 0) {
            $url = 'default_url';
            unset($p_params[$index]);
            $p_params['options'] = implode(', ', $p_params);
            break;
        }
    }

    // sets the URL parameter option
    $context->$url->uri_parameter = $p_params['options'];
	
    return $context->$url->url_parameters;
} // fn smarty_function_urlparameters

?>
