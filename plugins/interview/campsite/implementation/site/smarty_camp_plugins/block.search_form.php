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
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    if (isset($p_content)) {
        $html = "<form name=\"search\" action=\"\" method=\"post\">\n"
            ."<input type=\"hidden\" name=\"f_tpl\" value=\"27\" />\n";
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"f_search\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" ".$p_params['html_code']." />\n</form>\n";
    }

    return $html;
} // fn smarty_block_search_form

?>