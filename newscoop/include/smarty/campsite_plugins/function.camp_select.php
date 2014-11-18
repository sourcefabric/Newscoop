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
 null|string     string the formatted date
 *     null in case a non-valid format was passed
 */
function smarty_function_camp_select($p_params, &$p_smarty)
{
    global $g_ado_db;

    $translator = \Zend_Registry::get('container')->getService('translator');

    $p_smarty->loadPlugin('smarty_function_html_options');

    if (!isset($p_params['object']) || !isset($p_params['attribute'])) {
        return;
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');
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
                . $p_params['html_code'] . ' /> '
                .smarty_function_escape_special_chars($p_params['female_name']);
        } elseif ($attribute == 'title') {
        	if (is_null($fieldValue)) {
                $fieldValue = $campsite->user->$attribute;
            }
            $selectTag = true;
            $output = array($translator->trans('Mr.', array(), 'users'), $translator->trans('Mrs.', array(), 'users'), $translator->trans('Ms.', array(), 'users'), $translator->trans('Dr.', array(), 'users'));
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
            $output = array($translator->trans('Corporate', array(), 'users'), $translator->trans('Non-Governmental', array(), 'users'), $translator->trans('Government Agency', array(), 'users'), $translator->trans('Academic', array(), 'users'), $translator->trans('Media', array(), 'users'), $translator->trans('Other', array(), 'users'));
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
    	$subsType = strtolower(CampRequest::GetVar('SubsType'));
    	if ($subsType != 'trial' && $subsType != 'paid') {
    		return null;
    	}
    	if ($attribute == 'languages') {
            $publicationLanguages = $campsite->publication->languages_list(false);
            foreach ($publicationLanguages as $language) {
                $output[] = $language->name;
                $values[] = $language->number;
            }
            $selectTag = true;
            $html = '<select name="subscription_language[]" multiple size="3" ';
            if ($subsType == 'paid') {
                $html .= 'onchange="update_subscription_payment();" ';
            }
            $html .= 'id="select_language" ' . $p_params['html_code'] . '>';
        } elseif ($attribute == 'alllanguages') {
        	$html = '<input type="checkbox" name="subs_all_languages" '
                .'onchange="ToggleElementEnabled(\'select_language\');';
            if ($subsType == 'paid') {
                $html .= ' update_subscription_payment();';
            }
            $html .= '" ' . $p_params['html_code'] . ' />';
        } elseif ($attribute == 'section') {
            if ($campsite->subs_by_type == 'publication') {
                $html = '<input type="hidden" name="cb_subs[]" value="'
                    .$campsite->section->number.'" />';
            } elseif ($campsite->subs_by_type == 'section') {
                $html = '<input type="checkbox" name="cb_subs[]" value="'
                    .$campsite->section->number.'" '
                    .'onchange="update_subscription_payment();" '
                    . $p_params['html_code'] . ' />';
            }
        }
        break;

    case 'search':
        if ($attribute == 'mode') {
            $html = '<input type="checkbox" name="f_match_all" '
            . $p_params['html_code'] . ' />';
        } elseif ($attribute == 'level') {
            $html = '<select name="f_search_'.$attribute.'" ' . $p_params['html_code'] . '>'
                .'<option value="1" selected="selected">' . $translator->trans('Publication') . '</option>'
                .'<option value="2">' . $translator->trans('Issue') . '</option>'
                .'<option value="3">' . $translator->trans('Section') . '</option>'
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
            $sectionsList = Section::GetList($constraints, array('Name'=>'ASC'), 0, 0, $count);
            
            $html = '<select name="f_search_section" ' . $p_params['html_code'] . '>';
            $html .= '<option value="0" selected="selected">' . $translator->trans('-- ALL SECTIONS --') . '</option>';
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
            $html .= '<option value="0" selected="selected">&nbsp;</option>';
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
