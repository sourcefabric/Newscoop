<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

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
    $urlString = '';
    $campContext = $p_smarty->get_template_vars('campsite');

    $validParams = array('language','publication','issue','section','article');
    if (empty($p_params)
            || in_array(strtolower($p_params['options']), $validParams)) {
        $urlString = $campContext->url->base;

        // uses the smarty camp plugin uri
        require_once $p_smarty->_get_plugin_filepath('function', 'uri');
        $urlString .= smarty_function_uri($p_params);
    }

    return $urlString;
} // fn smarty_function_url

?>