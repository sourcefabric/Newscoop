<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite formparameters function plugin
 *
 * Type:     function
 * Name:     formparameters
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $uriString
 *      The Form parameters requested
 */
function smarty_function_formparameters($p_params, &$p_smarty)
{
    $context = $p_smarty->get_template_vars('campsite');
    $formParameters = $context->url->form_parameters;

    $html = '';
    foreach ($formParameters as $param) {
        $html .= '<input type="hidden" name="'.$param['name'].'" value="'.$param['value']."\" />\n";
    }

    return $html;
} // fn smarty_function_formparameters

?>