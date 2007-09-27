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
    if (empty($p_params)
            || in_array(strtolower($p_params['options']), $validParams)) {
        require_once $p_smarty->_get_plugin_filepath('function', 'uri');
        $uriString = smarty_function_uri($p_params);
    }

    return $uriString;
} // fn smarty_function_uripath

?>