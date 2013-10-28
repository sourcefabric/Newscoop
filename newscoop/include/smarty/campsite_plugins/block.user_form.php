<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite user_form block plugin
 *
 * Type:     block
 * Name:     user_form
 * Purpose:  Provides a...
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_user_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{

    $p_smarty->smarty->loadPlugin('smarty_function_get_resource_id');
    $resourceId = smarty_function_get_resource_id($p_params, $p_smarty);

	if (!isset($p_content)) {
        return null;
    }

    $translator = \Zend_Registry::get('container')->getService('translator');
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    $campsite = $p_smarty->getTemplateVars('gimme');

    $url = $campsite->url;
    $url->uri_parameter = "";
    $template = null;

    if (isset($p_params['template'])) {
        $template = new MetaTemplate($resourceId);
        if (!$template->defined()) {
            CampTemplate::singleton()->trigger_error('invalid template "' . $p_params['template']
            . '" specified in the user form');
            return false;
        }
    } elseif (is_numeric($url->get_parameter('tpl'))) {
        $template = $campsite->default_template;
    }
    if (!isset($p_params['submit_button'])) {
    	$p_params['submit_button'] = $translator->trans('Submit');
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }
    if (!isset($p_params['button_html_code']) || empty($p_params['button_html_code'])) {
        $p_params['button_html_code'] = '';
    }

    if (isset($template)) {
        $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
    }
    if ($campsite->user->defined && $campsite->user->subscription->defined) {
        $subsType = $campsite->user->subscription->type == 'T' ? 'trial' : 'paid';
    } else {
        $subsType = null;
    }
    $html = "<form name=\"edit_user\" action=\"" . $url->uri_path
    . "\" method=\"post\" ".$p_params['html_code'].">\n";
    if (!is_null($subsType)) {
        $html .= "<input type=\"hidden\" name=\"f_substype\" value=\"".$subsType."\" />\n";
    }
    if (isset($template)) {
        $html .= "<input type=\"hidden\" name=\"tpl\" value=\"".$template->identifier."\" />\n";
    }
    foreach ($campsite->url->form_parameters as $param) {
        if ($param['name'] == 'tpl') {
            continue;
        }
        $html .= '<input type="hidden" name="'.$param['name']
        .'" value="'.htmlentities($param['value'])."\" />\n";
    }
    $html.= $p_content;
    $html.= "<input type=\"submit\" name=\"f_edit_user\" value=\""
    .smarty_function_escape_special_chars($p_params['submit_button'])
    ."\" ".$p_params['button_html_code']." />\n</form>\n";

    return $html;
} // fn smarty_block_user_form

?>
