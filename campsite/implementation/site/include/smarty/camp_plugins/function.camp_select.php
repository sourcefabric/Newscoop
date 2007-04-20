<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite camp_select function plugin
 *
 * Type:     function
 * Name:     camp_select
 * Purpose:  Provides a...
 *
 * @param string
 *     $p_unixtime the date in unixtime format from $smarty.now
 * @param string
 *     $p_format the date format wanted
 *
 * @return
 *     string the formatted date
 *     null in case a non-valid format was passed
 */
function smarty_function_camp_select($p_params, &$p_smarty)
{
    global $g_ado_db;

    require_once $p_smarty->_get_plugin_filepath('function','html_options');

    if (!isset($p_params['object']) || !isset($p_params['attribute'])) {
        return;
    }

    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    $html = '';

    $object = strtolower($p_params['object']);
    $attribute = strtolower($p_params['attribute']);
    $selectTag = false;

    switch($object) {
    case 'user':
        $attrValue = $camp->$object->$attribute;
        if ($attribute == 'gender') {
            $html = '<input type="radio" name="f_user_'.$attribute
                .'" value="M" '.(($attrValue == 'M') ? 'checked' : '').' /> '
                .smarty_function_escape_special_chars($p_params['male_name'])
                .' <input type="radio" name="f_user_'.$attribute
                .'" value="F" '.(($attrValue == 'F') ? 'checked' : '').' /> '
                .smarty_function_escape_special_chars($p_params['female_name']);
        } elseif ($attribute == 'title') {
            $selectTag = true;
            $output = array('Mr.', 'Mrs.', 'Ms.', 'Dr.');
            $values = array('Mr.', 'Mrs.', 'Ms.', 'Dr.');
            $html = '<select name="f_user_'.$attribute.'">';
        } elseif ($attribute == 'country') {
            $sqlQuery = 'SELECT Code, Name FROM Countries '
                       .'GROUP BY Code ASC ORDER BY Name ASC';
            $data = $g_ado_db->GetAll($sqlQuery);
            foreach($data as $country) {
                $output[] = $country['Name'];
                $values[] = $country['Code'];
            }
            $selectTag = true;
            $html = '<select name="f_user_'.$attribute.'">';
        } elseif ($attribute == 'age') {
            $selectTag = true;
            $output = array('0-17', '18-24', '25-39', '40-49', '50-65', '65 or over');
            $values = array('0-17', '18-24', '25-39', '40-49', '50-65', '65-');
            $html = '<select name="f_user_'.$attribute.'">';
        } elseif ($attribute == 'employertype') {
            $selectTag = true;
            $output = array('Corporate', 'Non-Governmental', 'Government Agency', 'Academic', 'Media', 'Other');
            $values = array('Corporate', 'NGO', 'Government Agency', 'Academic', 'Media', 'Other');
            $html = '<select name="f_user_'.$attribute.'">';
        } elseif (substr($attribute, 0, 4) == 'pref') {
            $html = '<input type="checkbox" name="f_user_'$attribute.'" '
                .(($attrValue == 'Y') ? ' value="on" checked />' : ' />')
                .'<input type="hidden" name="f_has_pref'
                .substr($attribute, 4, 1).'" value="1" />';
        }
        break;

    case 'login':
        if ($attribute == 'rememberuser') {
            $html = '<input type="checkbox" name="f_login_'.$attribute.'" />';
        }
        break;

    case 'subscription':
        if ($attribute == 'languages') {
            $sqlQuery = "SELECT l.Id, l.OrigName "
                ."FROM Issues as i, Languages as l "
                ."WHERE  i.IdLanguage = l.Id and i.IdPublication = "
                .$camp->publication->id
                ."GROUP BY l.Id";
            $data = $g_ado_db->GetAll($sqlQuery);
            foreach ($data as $language) {
                $output[] = $language['Id'];
                $values[] = $language['OrigName'];
            }
            $selectTag = true;
            $html = '<select name="subscription_language[]" '
                .'size="3" ' // TODO set the size value
                .' ' // TODO set multipleability
                .'onchange="update_subscription_payment();" '
                .'id="select_language">';
        } elseif ($attribute == 'alllanguages') {
            $html = '<input type="checkbox" name="subs_all_languages" '
                .'onchange="update_subscription_payment(); '
                .'ToggleElementEnabled(\'select_language\');" />';
        } elseif ($attribute == 'section') {
            if (1) {
                $html = '<input type="hidden" name="cb_subs[]" value="'
                    .$camp->section->number.'" ';
            } else {
                $html = '<input type="checkbox" name="cb_subs[]" value="'
                    .$camp->section->number.'" '
                    .'onchange="update_subscription_payment();" ';
            }
        }
        break;

    case 'search':
        if ($attribute == 'mode') {
            $html = '<input type="checkbox" name="f_search_'.$attribute.'" />';
        } elseif ($attribute == 'level') {
            $html = '<select name="f_search_'.$attribute.'">'
                .'<option value="0">Publication</option>'
                .'<option value="1">Issue</option>'
                .'<option value="2">Section</option>'
                .'</select>';
        }
    }

    if ($selectTag == true) {
        $html.= smarty_function_html_options(array('output' => $output,
                                                   'values' => $values,
                                                   'selected' => $attrValue,
                                                   'print_result' => false),
                                             $p_smarty);
        $html.= '</select>';
    }

    return $html;
} // fn smarty_function_camp_select

?>