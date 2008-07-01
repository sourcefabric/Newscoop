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
function smarty_function_interview_edit($p_params, &$p_smarty)
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
    if (!isset($p_params['size']) || !ctype_digit($p_params['size'])) {
        $p_params['size'] = 10;
    }
    if (!isset($p_params['format']) || empty($p_params['format'])) {
        $p_params['format'] = 'Y-m-d';
    }
    $object = strtolower($p_params['object']);
    $attribute = strtolower($p_params['attribute']);

    // gets the attribute value from the context
    $attrValue = $campsite->interview->$attribute;

    $txtFields = array('title', 'questions_limit', 'image_description'); 
    $dateFields = array('interview_begin', 'interview_end', 'questions_begin', 'questions_end');
    $txtAreaFields = array('description_short', 'description');
    $selectFields = array('language', 'guest', 'moderator', 'status');
    $fileFields = array('image');
    $checkBoxes = array('image_delete');

    if (in_array($attribute, $txtAreaFields)) {
        $html = '<textarea name="f_interview_'.$attribute.'" cols="40" rows="4" '.$p_params['html_code'].'>';
        $html .= isset($_REQUEST["f_interview_$attribute"]) ? 
            smarty_function_escape_special_chars($_REQUEST["f_interview_$attribute"]) : 
            smarty_function_escape_special_chars($attrValue);
        $html .= '</textarea>';
            
    } elseif (in_array($attribute, $txtFields)) {
        $html = '<input type="text" name="f_interview_'.$attribute.'" size="'.($length > 32 ? 32 : $length).'" maxlength="'.$length.'" ';
        if (isset($_REQUEST["f_interview_$attribute"])) {
            $html .= 'value="'.smarty_function_escape_special_chars($_REQUEST["f_interview_$attribute"]).'" ';
        } elseif (isset($attrValue)) {
            $html .= 'value="'.smarty_function_escape_special_chars($attrValue).'" ';
        }
        $html .= $p_params['html_code'].' />';
        
    } elseif (in_array($attribute, $dateFields)) {
        if ($p_params['format']) 
        $html = '<input type="text" name="f_interview_'.$attribute.'" size="'.($length > 32 ? 32 : $length).'" maxlength="'.$length.'" ';
        if (isset($_REQUEST["f_interview_$attribute"])) {
            $html .= 'value="'.smarty_function_escape_special_chars($_REQUEST["f_interview_$attribute"]).'" ';
        } elseif (isset($attrValue)) {
            $html .= 'value="'.smarty_function_escape_special_chars(date('Y-m-d', $attrValue)).'" ';
        }
        $html .= $p_params['html_code'].' />';
        
    } elseif (in_array($attribute, $selectFields)) {
        require_once $p_smarty->_get_plugin_filepath('function','html_options');
        
        switch ($attribute) {
            case 'language':
                foreach (Language::getLanguages() as $Language) {
                    $options[$Language->getProperty('Id')] = $Language->getName();   
                }
                asort($options);
                $html = '<select name="f_interview_language_id" id="interview_"'.$attribute.'>';
                $html.= smarty_function_html_options(array(
                    'options' => $options,
                    'selected' => isset($_REQUEST['f_interview_language_id']) ? $_REQUEST['f_interview_language_id']: $attrValue->number,
                    'print_result' => false),
                    $p_smarty
                );
                $html .= '</select>';
            break;
            
            case 'guest':
            case 'moderator':
                foreach (User::getUsers() as $User) {
                    if ($User->hasPermission('plugin_interview_'.$attribute)) {
                        $options[$User->getProperty('Id')] = $User->getProperty('Name');
                    }  
                }
                asort($options);
                $html = '<select name="f_interview_'.$attribute.'_user_id" id="interview_"'.attribute.'>';
                $html.= smarty_function_html_options(array(
                    'options' => $options,
                    'selected' => isset($_REQUEST['f_interview_'.$attribute.'_user_id']) ? $_REQUEST['f_interview_'.$attribute.'_user_id']: $attrValue->identifier,
                   'print_result' => false),
                    $p_smarty
                );
                $html .= '</select>';
            break;
            
            case 'status':
                $options = array('draft' => 'draft', 'pending' => 'pending', 'published' => 'published', 'rejected', 'rejected');   

                $html = '<select name="f_interview_status" id="interview_"'.$attribute.'>';
                $html.= smarty_function_html_options(array(
                    'options' => $options,
                    'selected' => isset($_REQUEST['f_interview_status']) ? $_REQUEST['f_interview_status']: $attrValue,
                    'print_result' => false),
                    $p_smarty
                );
                $html .= '</select>';
            break;
        }  
          
    } elseif (in_array($attribute, $fileFields)) {
        $html = '<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />';
        $html .= '<input type="file" name="f_interview_'.$attribute.'" '.$p_params['html_code'].' />'; 
         
    } elseif (in_array($attribute, $checkBoxes)) {
        $html .= '<input type="checkbox" name="f_interview_'.$attribute.'" '.$p_params['html_code'].' />';  
    }

    return $html;
} // fn smarty_function_interview_edit

?>