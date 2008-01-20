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
    $validParams = array('language','publication','issue','section',
                         'article','articleattachment','image', 'template');
    $option = null;
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

    return $context->url->url_parameters;
} // fn smarty_function_urlparameters

?>