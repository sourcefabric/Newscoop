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
    // sets the URL parameter option
    $context->url->uri_parameter = strtolower($p_params['options']);

    return $context->url->uri_path;
} // fn smarty_function_uripath

?>