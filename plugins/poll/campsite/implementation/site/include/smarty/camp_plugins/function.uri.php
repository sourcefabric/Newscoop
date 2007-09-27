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
    $uriString = '';
    $validParams = array('language','publication','issue','section','article');
    if (!empty($p_params['options'])) {
        $option = strtolower($p_params['options']);
    }

    if (!isset($p_params['options']) || in_array($option, $validParams)) {
        $context = $p_smarty->get_template_vars('campsite');
        if (!is_object($context->url)) {
            return null;
        }
        // sets the URL parameter option
        $context->url->uri_parameter = $option;
        // gets the URI
        $uriString = $context->url->uri;
    }

    return $uriString;
} // fn smarty_function_uri

?>