<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite blogcomment_form block plugin
 *
 * Type:     block
 * Name:     blogcomment_form
 * Purpose:  Provides a form for an blog comment
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
function smarty_block_blogcomment_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    if (!isset($p_content)) {
        return '';
    }

    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    
    if (!is_object($campsite->blogentry) || !$campsite->blogentry->identifier) {
        return '';   
    }

    $url = $campsite->url;
    $url->uri_parameter = "";
    $template = null;
    if (isset($p_params['template'])) {
        $template = new MetaTemplate($p_params['template']);
        if (!$template->defined) {
            CampTemplate::trigger_error('invalid template "' . $p_params['template']
            . '" specified in the blog comment form');
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
    $html = "<form name=\"blogcomment\" action=\"" . $url->uri_path . "\" method=\"post\" "
    .$p_params['html_code'].">\n";
    $html .= "<input type=\"hidden\" name=\"f_blogentry_id\" value=\"{$campsite->blogentry->identifier}\" />\n";
    
    if (isset($template)) {
        $html .= "<input type=\"hidden\" name=\"tpl\" value=\"" . $template->identifier . "\" />\n";
    }
    foreach ($campsite->url->form_parameters as $param) {
        if ($param['name'] == 'tpl') {
            continue;
        }
        $html .= '<input type="hidden" name="'.$param['name']
        .'" value="'.htmlentities($param['value'])."\" />\n";
    }
    $html .= $p_content;
    
    $html .= "<input type=\"submit\" name=\"f_submit_blogcomment\" "
    ."id=\"blogcomment_submit\" value=\""
    .smarty_function_escape_special_chars($p_params['submit_button'])
    ."\" " . $p_params['button_html_code'] . " />\n";
    if (isset($p_params['preview_button']) && !empty($p_params['preview_button'])) {
        $html .= "<input type=\"submit\" name=\"f_preview_blogcomment\" "
        ."id=\"blogcomment_preview\" value=\""
        .smarty_function_escape_special_chars($p_params['preview_button'])
        ."\" " . $p_params['button_html_code'] . " />\n";
    }
    $html .= "</form>\n";

    return $html;
} // fn smarty_block_blogcomment_form

?>