<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];


/**
 * Campsite subscription_form block plugin
 *
 * Type:     block
 * Name:     subscription_form
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
function smarty_block_subscription_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    if (!isset($p_params['template'])) {
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }

    if (isset($p_content)) {
        $html = "<form name=\"subscription_form\" action=\"\" method=\"post\">\n"
            ."<input type=\"hidden\" name=\"tpl\" value=\"27\" />\n"
            ."<input type=\"hidden\" name=\"SubsType\" value=\"\" />\n"
            ."<input type=\"hidden\" name=\"tx_subs\" value=\"\" />\n"
            ."<input type=\"hidden\" name=\"nos\" value=\"\" />\n"
            ."<input type=\"hidden\" name=\"unitcost\" value=\"\" />\n"
            ."<input type=\"hidden\" name=\"unitcostalllang\" value=\"\" />\n";
        if ($subsType == 'paid' && $total != '') {
            $html .= $total." <input type=\"text\" name=\"suma\" size=\"10\" "
                ."READONLY /> ".$currency;
        }


        if ($subsType == 'paid' && $evaluate != '') {
            $html .= "<p><input type=\"button\" value=\"\" "
                ."onclick=\"update_subscription_payment();\" /></p>";
        }


        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"submit_comment\" "
            ."id=\"articleCommentSubmit\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" />\n";
        if (isset($p_params['preview_button']) && !empty($p_params['preview_button'])) {
            $html .= "<input type=\"submit\" name=\"previewComment\" "
                ."id=\"articleCommentPreview\" value=\""
                .smarty_function_escape_special_chars($p_params['preview_button'])
                ."\" />\n";
        }
        $html .= "</form>\n";
    }

    return $html;
} // fn smarty_block_subscription_form

?>