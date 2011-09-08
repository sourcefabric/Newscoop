<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

require_once dirname(__FILE__) . '/function.uri.php';

/**
 * Campsite url function plugin
 *
 * Type:     function
 * Name:     url
 * Purpose:
 *
 * @param array $p_params
 * @param object $p_smarty
 *      The Smarty object
 *
 * @return string $urlString
 *      The full URL string
 */
function smarty_function_url($p_params, &$p_smarty)
{
    $context = $p_smarty->getTemplateVars('gimme');
    // gets the URL base
    $urlString = $context->url->base;

    // appends the URI path and query values to the base
    $urlString .= smarty_function_uri($p_params, $p_smarty);

    return $urlString;
} // fn smarty_function_url

?>
