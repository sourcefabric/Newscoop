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
 string     string the html form element
 *     string empty if something is wrong
 */
function smarty_function_camp_edit($p_params, &$p_smarty)
{
    global $g_ado_db;
    
    static $calendarIncludesSent = false;

    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');
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
        $fieldValue = $campsite->default_url->get_parameter('f_user_'.$attribute);
        if (is_null($fieldValue)
        && strncasecmp($attribute, 'password', strlen('password')) != 0
        && strcasecmp($attribute, 'subscription') != 0) {
            $fieldValue = $campsite->user->$attribute;
        }
        
        $passwdFields = array('password','passwordagain');
        $txtAreaFields = array('interests','improvements','text1','text2','text3');
        $otherFields = array('name', 'uname', 'email', 'city', 'str_address', 'state',
        'phone', 'fax', 'contact', 'second_phone', 'postal_code', 'employer', 'position',
        'how', 'languages', 'field1', 'field2', 'field3', 'field4', 'field5');

        if (in_array($attribute, $passwdFields)) {
            $html = '<input type="password" name="f_user_'.$attribute.'" size="32" '
                .'maxlength="32" '.$p_params['html_code'].' value="'
                .smarty_function_escape_special_chars($fieldValue).'" />';
        } elseif (in_array($attribute, $txtAreaFields)) {
        	$cols = isset($p_params['columns']) ? (int)$p_params['columns'] : 40;
        	$rows = isset($p_params['rows']) ? (int)$p_params['rows'] : 4;
        	$html = '<textarea name="f_user_'.$attribute."\" cols=\"$cols\" rows=\"$rows\" "
                .$p_params['html_code'].'>'
                .smarty_function_escape_special_chars($fieldValue).'</textarea>';
        } elseif (in_array($attribute, $otherFields)) {
            $length = substr($row['Type'], strpos($row['Type'], '(') + 1, -1);
            $html = '<input type="text" name="f_user_'.$attribute
                .'" size="'.($length > 32 ? 32 : $length)
                .'" maxlength="'.$length.'" '
                .'value="'.smarty_function_escape_special_chars($fieldValue).'" ';
            $html .= $p_params['html_code'].' />';
        }
        break;

    case 'subscription':
        $html = '<input type="hidden" name="f_subs_'.$campsite->section->number
            .'" value="'. $campsite->publication->subscription_time.'" '
            .$p_params['html_code'].' />'.$campsite->publication->subscription_time;
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
        } elseif ($attribute == 'start_date' || $attribute == 'end_date') {
        	if (!$calendarIncludesSent) {
        		$html = camp_get_calendar_include($campsite->language->code);
        		$calendarIncludesSent = true;
        	}
        	$html .= camp_get_calendar_field('f_search_' . $attribute, null, $p_params['html_code']);
        }
        break;

    case 'comment':
        if ($campsite->article->comments_enabled == 1) {
            $fieldValue = $campsite->default_url->get_parameter('f_comment_'.$attribute);
            if ($attribute == 'content') {
            	$cols = isset($p_params['columns']) ? (int)$p_params['columns'] : 40;
            	$rows = isset($p_params['rows']) ? (int)$p_params['rows'] : 4;
                $html = '<textarea name="f_comment_'.$attribute."\" cols=\"$cols\" rows=\"$rows\" "
                    .$p_params['html_code'].'>'
                    .smarty_function_escape_special_chars($fieldValue)
                    .'</textarea>';
            } elseif ($attribute == 'subject' || $attribute == 'reader_email'
                      || $attribute == 'nickname') {
                $html = '<input type="text" name="f_comment_'.$attribute
                    .'" maxlength="255" size="'.$p_params['size'].'" value="'
                    .smarty_function_escape_special_chars($fieldValue)
                    .'" '.$p_params['html_code'].' />';
            } elseif ($attribute == 'is_anonymous') {
                $html = '<input type="hidden" name="f_comment_'.$attribute
					.'" value="0"><input type="checkbox" name="f_comment_'.$attribute
                    .'" value="1" '.$p_params['html_code'].' />';
            } elseif ($attribute == 'parent') {
                $html = '<input type="hidden" name="f_comment_parent" '.$p_params['html_code'].' />';
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
