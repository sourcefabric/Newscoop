<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite search_form block plugin
 *
 * Type:     block
 * Name:     search_form
 * Purpose:  Provides a...
 *
 * @param string $p_params
 *
 * @param string $p_content
 *
 * @param string $p_smarty
 *
 *
 * @return string $html
 */
function smarty_block_search_form($p_params, $p_content, &$p_smarty)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_params['template'])) {
        CampTemplate::trigger_error('The template for the search form was not specified');
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    $template = new MetaTemplate($p_params['template']);
    if (!$template->defined) {
        CampTemplate::trigger_error('The template "' . $template->name
        . '", specified in the search form is invalid.');
        return false;
    }

    if (isset($p_content)) {
        $url = $campsite->url;
        $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
        $html = "<form name=\"search_articles\" action=\"" . $url->uri_path . "\" method=\"post\">\n"
        ."<input type=\"hidden\" name=\"tpl\" value=\"" . $template->identifier . "\" />\n";
        foreach ($campsite->url->form_parameters as $param) {
            $html .= '<input type="hidden" name="'.$param['name']
                .'" value="'.htmlentities($param['value'])."\" />\n";
        }
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"f_search_articles\" value=\""
        .smarty_function_escape_special_chars($p_params['submit_button'])
        ."\" ".$p_params['html_code']." />\n</form>\n";
    }

    return $html;
} // fn smarty_block_search_form

?>