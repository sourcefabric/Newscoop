<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];


/**
 * Campsite search_form block plugin
 *
 * Type:     block
 * Name:     search_form
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
function smarty_block_search_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    if (!isset($p_params['template'])) {
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }

    if (isset($p_content)) {
        $html = "<form name=\"search\" action=\"\" method=\"post\">\n"
            ."<input type=\"hidden\" name=\"tpl\" value=\"27\" />\n";
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"search\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" />\n</form>\n";
    }

    return $html;
} // fn smarty_block_search_form

?>