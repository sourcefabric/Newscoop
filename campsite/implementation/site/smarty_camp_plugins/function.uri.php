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
    $validParams = array('language','publication','issue','section',
                         'article','articleattachment','image', 'template',
                         'previous_items', 'next_items');
    if (!empty($p_params['options'])) {
        $optionsString = strtolower($p_params['options']);
        $options = preg_split('/ /', $p_params['options']);
        $option = $options[0];
    }

    $context = $p_smarty->get_template_vars('campsite');
    if (isset($option) && in_array($option, $validParams)) {
        // sets the URL parameter option
        $context->url->uri_parameter = $optionsString;
    } else {
        $context->url->uri_parameter = null;
    }

    return $context->url->uri;
} // fn smarty_function_uri

?>