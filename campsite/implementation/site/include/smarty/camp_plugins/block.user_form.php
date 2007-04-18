<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];


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
    if (!isset($p_params['template'])) {
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }

    if (isset($p_content)) {
        $html = "<form name=\"user\" action=\"peco.html\" method=\"post\">\n"
            ."<input type=\"hidden\" name=\"tpl\" value=\"27\" />\n"
            ."<input type=\"hidden\" name=\"f_substype\" value=\"paid\" />\n";
        $html.= $p_content;
        $html.= "<input type=\"submit\" name=\"f_useradd\" value=\""
            .$p_params['submit_button']."\" />\n</form>\n";
    }

    return $html;
} // fn smarty_block_user_form

?>