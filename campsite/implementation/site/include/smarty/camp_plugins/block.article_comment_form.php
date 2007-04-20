<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


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

    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    $html = '';

    if ($camp->articlecomment->enabled == true) {
        return $html;
    }
    if (!isset($p_params['template'])) {
        return false;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }

    if (isset($p_content)) {
        $html = "<form name=\"article_comment\" action=\"\" method=\"post\">\n"
               ."<input type=\"hidden\" name=\"tpl\" value=\""
               .$camp->template->id."\" />\n";
        if ($camp->url->type == 'short names') {
            $html .= "<input type=\"hidden\" name=\"f_language_id\" "
                ."value=\"".$camp->language->id."\" />\n"
                ."<input type=\"hidden\" name=\"f_publication_id\" "
                ."value=\"".$camp->publication->id."\" />\n"
                ."<input type=\"hidden\" name=\"f_issue_nr\" "
                ."value=\"".$camp->issue->number."\" />\n"
                ."<input type=\"hidden\" name=\"f_section_nr\" "
                ."value=\"".$camp->section->number."\" />\n"
                ."<input type=\"hidden\" name=\"f_article_nr\" "
                ."value=\"".$camp->article->number."\" />\n";
        }
    if ($camp->articlecomment->id > 0) {
            $html .= "<input type=\"hidden\" name=\"f_acid\" "
                ."value=\"".$camp->articlecomment->id."\" />\n";
        }
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"f_submit_comment\" "
            ."id=\"article_comment_submit\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" />\n";
        if (isset($p_params['preview_button']) && !empty($p_params['preview_button'])) {
            $html .= "<input type=\"submit\" name=\"f_preview_comment\" "
                ."id=\"article_comment_preview\" value=\""
                .smarty_function_escape_special_chars($p_params['preview_button'])
                ."\" />\n";
        }
        $html .= "</form>\n";
    }

    return $html;
} // fn smarty_block_article_comment_form

?>