<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite poll_form block plugin
 *
 * Type:     block
 * Name:     poll_form
 * Purpose:  Displayes the poll for voting
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
function smarty_block_poll_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $camp = $p_smarty->get_template_vars('campsite');
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
        $html = "<form name=\"poll\" action=\"\" method=\"post\">\n"
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
        $html .= "<input type=\"hidden\" name=\"f_poll_nr\" "
            ."value=\"".$camp->poll->poll_nr."\" />\n"
            ."<input type=\"hidden\" name=\"f_poll_language_id\" "
            ."value=\"".$camp->poll->language_id."\" />\n";

        $html .= $p_content;
        
        $html .= "<input type=\"submit\" name=\"f_poll_submit\" "
            ."id=\"poll_submit\" value=\""
            .smarty_function_escape_special_chars($p_params['submit_button'])
            ."\" />\n";

        $html .= "</form>\n";
    }

    return $html;
} // fn smarty_block_poll_comment_form

?>