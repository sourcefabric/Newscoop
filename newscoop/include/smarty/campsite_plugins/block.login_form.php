<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite login_form block plugin
 *
 * Type:     block
 * Name:     login_form
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
 string|false
 */
function smarty_block_login_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    if (!isset($p_content)) {
        return '';
    }

    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');

    $url = $campsite->url;
    $url->uri_parameter = "";
    $template = null;
    if (isset($p_params['template'])) {
        $themePath = $campsite->issue->defined() ? $campsite->issue->theme_path : $campsite->publication->theme_path;
        $template = new MetaTemplate($p_params['template'], $themePath);
        if (!$template->defined()) {
            CampTemplate::singleton()->trigger_error('invalid template "' . $p_params['template']
            . '" specified in the login form');
            return false;
        }
    } elseif (is_numeric($url->get_parameter('tpl'))) {
        $template = $campsite->default_template;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
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
    $html = "<form name=\"login\" action=\"" . $url->uri_path . "\" method=\"post\" "
    . $p_params['html_code'] . ">\n";
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
    $html .= $p_content;
    $html .= "<input type=\"submit\" name=\"f_login\" value=\""
    .smarty_function_escape_special_chars($p_params['submit_button'])
    ."\" ".$p_params['button_html_code']." />\n</form>\n";

    return $html;
} // fn smarty_block_login_form

?>
