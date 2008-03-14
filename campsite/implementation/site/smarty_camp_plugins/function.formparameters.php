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

    if (!isset($p_params['options'])) {
        $formParameters = $context->url->form_parameters;
    } else {
        $option = null;
        $options = preg_split('/ /', $p_params['options']);
        $option = strtolower($options[0]);
        switch($option) {
        case 'from_start':
            $formParameters = $context->default_url->form_parameters;
            break;
        case 'article_comment':
            $formParameters = $context->url->form_parameters;
            if ($context->comment->defined == true) {
                $i = sizeof($formParameters);
                $formParameters[$i] = array('name' => 'IdComment',
                                            'value' => $context->comment->identifier);
            }
            break;
        default:
            // trigger error invalid option
            return false;
        }
    }

    $html = '';
    foreach ($formParameters as $param) {
        $html .= '<input type="hidden" name="'.$param['name'].'" value="'.$param['value']."\" />\n";
    }

    return $html;
} // fn smarty_function_formparameters

?>