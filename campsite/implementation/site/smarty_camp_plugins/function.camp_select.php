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
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    $object = strtolower($p_params['object']);
    $attribute = strtolower($p_params['attribute']);
    $selectTag = false;

    switch($object) {
    case 'user':
        $fieldValue = CampRequest::GetVar('f_user_'.$attribute);
        if ($attribute == 'gender') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $html = '<input type="radio" name="f_user_'.$attribute
                .'" value="M" '.(($fieldValue == 'M') ? 'checked' : '').' '
                . $p_params['html_code'] . '/> '
                .smarty_function_escape_special_chars($p_params['male_name'])
                .' <input type="radio" name="f_user_'.$attribute
                .'" value="F" '.(($fieldValue == 'F') ? 'checked' : '').' '
                . $p_params['html_code'] . '/> '
                .smarty_function_escape_special_chars($p_params['female_name']);
        } elseif ($attribute == 'title') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $selectTag = true;
            $output = array('Mr.', 'Mrs.', 'Ms.', 'Dr.');
            $values = array('Mr.', 'Mrs.', 'Ms.', 'Dr.');
            $html = '<select name="f_user_'.$attribute.'" ' . $p_params['html_code'] . '>';
        } elseif ($attribute == 'country') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->country_code;
            }
            $sqlQuery = 'SELECT Code, Name FROM Countries '
                       .'GROUP BY Code ASC ORDER BY Name ASC';
            $data = $g_ado_db->GetAll($sqlQuery);
            foreach($data as $country) {
                $output[] = $country['Name'];
                $values[] = $country['Code'];
            }
            $selectTag = true;
            $html = '<select name="f_user_'.$attribute.'" ' . $p_params['html_code'] . '>';
        } elseif ($attribute == 'age') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $selectTag = true;
            $output = array('0-17', '18-24', '25-39', '40-49', '50-65', '65 or over');
            $values = array('0-17', '18-24', '25-39', '40-49', '50-65', '65-');
            $html = '<select name="f_user_'.$attribute.'" ' . $p_params['html_code'] . '>';
        } elseif ($attribute == 'employertype') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $selectTag = true;
            $output = array('Corporate', 'Non-Governmental', 'Government Agency', 'Academic', 'Media', 'Other');
            $values = array('Corporate', 'NGO', 'Government Agency', 'Academic', 'Media', 'Other');
            $html = '<select name="f_user_'.$attribute.'" ' . $p_params['html_code'] . '>';
        } elseif (substr($attribute, 0, 4) == 'pref') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $html = '<input type="checkbox" name="f_user_'.$attribute.'" '
                .(($attrValue == 'Y') ? ' value="on" checked />' : ' />')
                .'<input type="hidden" name="f_has_pref'
                .substr($attribute, 4, 1).'" value="1" ' . $p_params['html_code'] . ' />';
        }
        break;

    case 'login':
        if ($attribute == 'rememberuser') {
            if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $html = '<input type="checkbox" name="f_login_'.$attribute.'" '
            . $p_params['html_code'] . ' />';
        }
        break;

    case 'subscription':
        if ($attribute == 'languages') {
            $sqlQuery = "SELECT l.Id, l.OrigName "
                ."FROM Issues as i, Languages as l "
                ."WHERE  i.IdLanguage = l.Id and i.IdPublication = "
                .$campsite->publication->identifier
                ." GROUP BY l.Id";
            $data = $g_ado_db->GetAll($sqlQuery);
            foreach ($data as $language) {
                $output[] = $language['OrigName'];
                $values[] = $language['Id'];
            }
            $selectTag = true;
            $html = '<select name="subscription_language[]" '
                .'size="3" ' // TODO set the size value
                .' ' // TODO set multipleability
                .'onchange="update_subscription_payment();" '
                .'id="select_language" ' . $p_params['html_code'] . '>';
        } elseif ($attribute == 'alllanguages') {
            $html = '<input type="checkbox" name="subs_all_languages" '
                .'onchange="update_subscription_payment(); '
                .'ToggleElementEnabled(\'select_language\');" '
                . $p_params['html_code'] . ' />';
        } elseif ($attribute == 'section') {
            if ($campsite->subs_by_type == 'publication') {
                $html = '<input type="hidden" name="cb_subs[]" value="'
                    .$campsite->section->number.'" >';
            } elseif ($campsite->subs_by_type == 'section') {
                $html = '<input type="checkbox" name="cb_subs[]" value="'
                    .$campsite->section->number.'" '
                    .'onchange="update_subscription_payment();" '
                    . $p_params['html_code'] . '>';
            }
        }
        break;

    case 'search':
        if ($attribute == 'mode') {
            $html = '<input type="checkbox" name="f_match_all" '
            . $p_params['html_code'] . ' />';
        } elseif ($attribute == 'level') {
        	if (!isGS('Publication')) {
        		camp_load_translation_strings("globals", $campsite->language->code);
        	}
            $html = '<select name="f_search_'.$attribute.'" ' . $p_params['html_code'] . '>'
                .'<option value="1" selected>' . getGS('Publication') . '</option>'
                .'<option value="2">' . getGS('Issue') . '</option>'
                .'<option value="3">' . getGS('Section') . '</option>'
                .'</select>';
        } elseif ($attribute == 'section') {
            $constraints = array();
            $operator = new Operator('is', 'integer');
            if ($campsite->publication->defined) {
            	$constraints[] = new ComparisonOperation('IdPublication', $operator, $campsite->publication->identifier);
            }
            if ($campsite->language->defined) {
            	$constraints[] = new ComparisonOperation('IdLanguage', $operator, $campsite->language->number);
            }
            if ($campsite->issue->defined) {
            	$constraints[] = new ComparisonOperation('NrIssue', $operator, $campsite->issue->number);
            }
            $sectionsList = Section::GetList($constraints, array('Number'=>'ASC'),
                                             0, 0, $count);
            if (!isGS('-- ALL SECTIONS --')) {
            	camp_load_translation_strings("user_subscription_sections", $campsite->language->code);
            }
            $html = '<select name="f_search_section" ' . $p_params['html_code'] . '>';
            $html .= '<option value="0" selected>' . getGS('-- ALL SECTIONS --') . '</option>';
            foreach ($sectionsList as $section) {
            	$html .= '<option value="' . $section->getSectionNumber() . '">'
            	      . htmlspecialchars($section->getName()) . '</option>';
            }
            $html .= '</select>';
        } elseif ($attribute == 'issue') {
        	$constraints = array();
            $operator = new Operator('is', 'integer');
            if ($campsite->publication->defined) {
                $constraints[] = new ComparisonOperation('IdPublication', $operator, $campsite->publication->identifier);
            }
            if ($campsite->language->defined) {
                $constraints[] = new ComparisonOperation('IdLanguage', $operator, $campsite->language->number);
            }
            $constraints[] = new ComparisonOperation('published', $operator, 'true');
            $issuesList = Issue::GetList($constraints,
                                         array(array('field'=>'bynumber', 'dir'=>'DESC')),
                                         0, 0, $count);
            $html = '<select name="f_search_issue" ' . $p_params['html_code'] . '>';
            $html .= '<option value="0" selected>&nbsp;</option>';
            foreach ($issuesList as $issue) {
            	$issueDesc = $issue->getIssueNumber() . '. '
            	           . $issue->getName()
            	           . ' ('. $issue->getPublicationDate() . ')';
                $html .= '<option value="' . $issue->getIssueNumber() . '">'
                      . htmlspecialchars($issueDesc) . '</option>';
            }
            $html .= '</select>';
        }
    }

    if ($selectTag == true) {
        $html.= smarty_function_html_options(array('output' => $output,
                                                   'values' => $values,
                                                   'selected' => $fieldValue,
                                                   'print_result' => false),
                                             $p_smarty);
        $html.= '</select>';
    }

    return $html;
} // fn smarty_function_camp_select

?>