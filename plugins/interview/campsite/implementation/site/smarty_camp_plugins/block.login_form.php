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
 *
 */
function smarty_block_login_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (isset($p_params['template'])) {
        $template = new MetaTemplate($p_params['template']);
        if (!$template->defined()) {
            $template = null;
        }
    }
    $templateId = is_null($template) ? $campsite->template->identifier : $template->identifier;
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    if (isset($p_content)) {
        $html = "<form name=\"login\" action=\"\" method=\"post\">\n"
            ."<input type=\"hidden\" name=\"f_tpl\" value=\"$templateId\" />\n";
        $html .= $p_content;
        $html .= "<div align=\"center\">";
        $html .= "<input type=\"submit\" name=\"f_login\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" ".$p_params['html_code']." />\n</div>\n</form>\n";
    }

    return $html;
} // fn smarty_block_login_form

?>