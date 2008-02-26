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
    $baseParameters = array(0 => array('object' => 'language',
                                       'id_field' => 'number',
                                       'form_name' => 'IdLanguage'),
                            1 => array('object' => 'publication',
                                       'id_field' => 'identifier',
                                       'form_name' => 'IdPublication'),
                            2 => array('object' => 'issue',
                                       'id_field' => 'number',
                                       'form_name' => 'NrIssue'),
                            3 => array('object' => 'section',
                                       'id_field' => 'number',
                                       'form_name' => 'NrSection'),
                            4 => array('object' => 'article',
                                       'id_field' => 'number',
                                       'form_name' => 'NrArticle'));

    $context = $p_smarty->get_template_vars('campsite');
    $html = '';

    foreach ($baseParameters as $param) {
        if ($context->$param['object']->defined == true) {
            $html .= '<input type="hidden" name="'.$param['form_name'].'" value="'.$context->$param['object']->$param['id_field']."\" />\n";
        }
    }

    $option = null;
    if (isset($p_params['options']) && !empty($p_params['options'])) {
        $options = preg_split('/ /', $p_params['options']);
        $option = strtolower($options[0]);
        switch($option) {
        case 'from_start':
            break;
        case 'article_comment':
            if ($context->comment->defined == true) {
                $html .= '<input type="hidden" name="IdComment" value="'.$context->comment->identifier."\" />\n";
            }
            break;
        default:
            // trigger error
            return false;
        }
    }

    return $html;
} // fn smarty_function_formparameters

?>