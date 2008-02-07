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
    $validParams = array('language','publication','issue','section',
                         'article','articleattachment','image', 'template');
    if (isset($p_params['options']) && !empty($p_params['options'])) {
        $optionsString = strtolower($p_params['options']);
        $options = preg_split('/ /', $p_params['options']);
        $option = $options[0];
    }

    $context = $p_smarty->get_template_vars('campsite');
    if (isset($option) && in_array($option, $validParams)) {
        // sets the URL parameter option
        $context->url->uri_parameter = $optionsString;
    }

    return $context->url->uri_path;
} // fn smarty_function_uripath

?>