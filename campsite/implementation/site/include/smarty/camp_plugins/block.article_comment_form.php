<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];


/**
 * Campsite article_comment_form block plugin
 *
 * Type:     block
 * Name:     article_comment_form
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
function smarty_block_article_comment_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    if (!isset($p_params['template'])) {
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }

    if (isset($p_content)) {
        $html = "<form name=\"article_comment\" action=\"\" method=\"post\">\n"
            ."<input type=\"hidden\" name=\"tpl\" value=\"27\" />\n";
        if (1) { // ($urlType == 'short names') {
            $html .= "<input type=\"hidden\" name=\"f_language_id\" "
                ."value=\"\" />\n"
                ."<input type=\"hidden\" name=\"f_publication_id\" "
                ."value=\"\" />\n"
                ."<input type=\"hidden\" name=\"f_issue_nr\" "
                ."value=\"\" />\n"
                ."<input type=\"hidden\" name=\"f_section_nr\" "
                ."value=\"\" />\n"
                ."<input type=\"hidden\" name=\"f_article_nr\" "
                ."value=\"\" />\n";
        }
    if (1) { // ($articleCommentId > 0) {
            $html .= "<input type=\"hidden\" name=\"f_acid\" "
                ."value=\"\" />\n";
        }
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"f_submit_comment\" "
            ."id=\"articleCommentSubmit\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" />\n";
        if (isset($p_params['preview_button']) && !empty($p_params['preview_button'])) {
            $html .= "<input type=\"submit\" name=\"f_preview_comment\" "
                ."id=\"articleCommentPreview\" value=\""
                .smarty_function_escape_special_chars($p_params['preview_button'])
                ."\" />\n";
        }
        $html .= "</form>\n";
    }

    return $html;
} // fn smarty_block_article_comment_form

?>