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
    $context = $p_smarty->get_template_vars('campsite');
    // sets the URL parameter option
    $context->url->uri_parameter = strtolower($p_params['options']);

    return $context->url->uri;
} // fn smarty_function_uri

?>