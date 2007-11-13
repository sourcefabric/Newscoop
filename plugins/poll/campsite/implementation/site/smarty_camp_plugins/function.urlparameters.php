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
        // gets the URI path
        $uriString = $context->url->url_parameters;
    }

    return $uriString;
} // fn smarty_function_urlparameters

?>