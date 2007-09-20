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
    $uriString = '';
    $validParams = array('language','publication','issue','section','article');
    if (!empty($p_params['options'])) {
        $option = strtolower($p_params['options']);
    }

    if (!isset($p_params['options']) || in_array($option, $validParams)) {
        $context = $p_smarty->get_template_vars('campsite');
        if (!isset($context->url)) {
            return null;
        }
        // sets the URL parameter option
        $context->url->uri_parameter = $option;
        // gets the URI path
        $uriString = $context->url->uri_path;
    }

    return $uriString;
} // fn smarty_function_uripath

?>