<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite camp_edit function plugin
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
function smarty_function_camp_edit($p_params, &$p_smarty)
{
    global $g_ado_db;

    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_params['object']) || !isset($p_params['attribute'])) {
        return $html;
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }
    if (!isset($p_params['size']) || !ctype_digit($p_params['size'])) {
        $p_params['size'] = 10;
    }

    $object = strtolower($p_params['object']);
    $attribute = strtolower($p_params['attribute']);

    switch ($object) {
    case 'user':
        // gets the attribute value from the context
        $attrValue = $campsite->$object->$attribute;

        $passwdFields = array('password','passwordagain');
        $txtAreaFields = array('interests','improvements','text1','text2','text3');

        if (in_array($attribute, $passwdFields)) {
            $html = '<input type="password" name="f_user_'.$attribute.'" size="32" '
                .'maxlength="32" '.$p_params['html_code'].' />';
        } elseif (in_array($attribute, $txtAreaFields)) {
            $html = '<textarea name="f_user_'.$attribute.'" cols="40" rows="4" '
                .$p_params['html_code'].'></textarea>';
        } elseif ($attribute != 'fk_user_type') {
            $sqlQuery = 'DESC liveuser_users '.$g_ado_db->escape($attribute);
            $row = $g_ado_db->GetRow($sqlQuery);
            if (!is_array($row) || sizeof($row) < 1) {
                return false;
            }
            $length = substr($row['Type'], strpos($row['Type'], '(') + 1, -1);
            $html = '<input type="text" name="f_user_'.$attribute
                .'" size="'.($length > 32 ? 32 : $length)
                .'" maxlength="'.$length.'" ';
            if (!empty($attrValue)) {
                $html .= 'value="'.smarty_function_escape_special_chars($attrValue).'" ';
            }
            $html .= $p_params['html_code'].' />';
        }
        break;

    case 'subscription':
        $html = '<input type="hidden" name="f_subs_'.$campsite->section->number
            .'" value="'. $campsite->subscription->subsTimeUnits.'" '
            .$p_params['html_code'].' />'.$campsite->subscription->subsTimeUnits;
        break;

    case 'login':
        if ($attribute == 'password') {
            $fieldType = 'password';
        } elseif ($attribute == 'uname') {
            $fieldType = 'text';
        }
        $html = '<input type="'.$fieldType.'" name="f_login_'.$attribute
            .'" maxlength="32" size="10" '.$p_params['html_code'].' />';
        break;

    case 'search':
        if ($attribute == 'keywords') {
            $html = '<input type="text" name="f_search_'.$attribute.'" '
                .'maxlength="255" size="'.$p_params['size'].'" value="';
            if (isset($campsite->search)) {
                $html.= smarty_function_escape_special_chars($campsite->search->keywords);
            }
            $html .= '" '.$p_params['html_code'].' />';
        }
        break;

    case 'comment':
        if ($campsite->article->comments_enabled == 1) {
            $fieldValue = CampRequest::GetVar('f_comment_'.$attribute);
            if ($attribute == 'content') {
                $html = '<textarea name="f_comment_'.$attribute.'" cols="40" rows="4" '
                    .$p_params['html_code'].'>'
                    .smarty_function_escape_special_chars($fieldValue)
                    .'</textarea>';
            } elseif ($attribute == 'subject' || $attribute == 'reader_email') {
                $html = '<input type="text" name="f_comment_'.$attribute
                    .'" maxlength="255" size="'.$p_params['size'].'" value="'
                    .smarty_function_escape_special_chars($fieldValue)
                    .'" '.$p_params['html_code'].' />';
            }
        }
        break;

    case 'captcha':
        $html = '<input type="text" name="f_captcha_code" '
            .'size="'.$p_params['size'].'" maxlength="255" '
            .$p_params['html_code'].' />';
        break;
    }

    return $html;
} // fn smarty_function_camp_edit

?>