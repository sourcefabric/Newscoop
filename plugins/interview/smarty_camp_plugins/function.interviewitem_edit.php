<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite interview_edit function plugin
 *
 * Type:     function
 * Name:     camp_edit
 * Purpose:  
 *
 * @param array
 *     $p_params the date in unixtime format from $smarty.now
 * @param object
 *     $p_smarty the date format wanted
 *
 * @return
 *     string the html form element
 *     string empty if something is wrong
 */
function smarty_function_interviewitem_edit($p_params, &$p_smarty)
{
    global $g_ado_db;

    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_params['attribute'])) {
        return $html;
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    $object = strtolower($p_params['object']);
    $attribute = strtolower($p_params['attribute']);

    // gets the attribute value from the context
    $attrValue = $campsite->interviewitem->$attribute;

    $txtAreaFields = array('question', 'answer');
    $selectFields = array('status');

    if (in_array($attribute, $txtAreaFields)) {
        $html = '<textarea name="f_interviewitem_'.$attribute.'" cols="40" rows="4" '.$p_params['html_code'].'>';
        $html .= isset($_REQUEST["f_interviewitem_$attribute"]) ? 
            smarty_function_escape_special_chars($_REQUEST["f_interviewitem_$attribute"]) : 
            smarty_function_escape_special_chars($attrValue);
        $html .= '</textarea>';
            
    } elseif (in_array($attribute, $selectFields)) {
        require_once $p_smarty->_get_plugin_filepath('function','html_options');
        
        switch ($attribute) {            
            case 'status':
                $options = array('draft' => 'draft', 'pending' => 'pending', 'published' => 'published', 'rejected' => 'rejected');   

                $html = '<select name="f_interviewitem_status" id="interview_"'.$attribute.'>';
                $html.= smarty_function_html_options(array(
                    'options' => $options,
                    'selected' => isset($_REQUEST['f_interview_status']) ? $_REQUEST['f_interview_status']: $attrValue,
                    'print_result' => false),
                    $p_smarty
                );
                $html .= '</select>';
            break;
        } 
    }
    
    return $html;
} // fn smarty_function_interview_edit

?>