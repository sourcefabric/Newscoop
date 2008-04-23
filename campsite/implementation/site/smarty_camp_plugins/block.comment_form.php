<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite comment_form block plugin
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
function smarty_block_comment_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once($p_smarty->_get_plugin_filepath('shared','escape_special_chars'));

    // gets the context variable
    $camp = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!$camp->article->comments_enabled) {
        return $html;
    }
    if (isset($p_params['template'])) {
        $template = new MetaTemplate($p_params['template']);
        if (!$template->defined()) {
            $template = $camp->template;
        }
    } else {
        $template = $camp->template;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    $anchor = isset($p_params['anchor']) ? '#'.$p_params['anchor'] : null;

    if (isset($p_content)) {
        $url = $camp->url;
        $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
        $html = "<form name=\"submit_comment\" action=\"" . $url->uri_path . "$anchor\" method=\"post\">\n"
               ."<input type=\"hidden\" name=\"tpl\" value=\"" . $template->identifier . "\" />\n";
        foreach ($camp->url->form_parameters as $param) {
            if ($param['name'] == 'tpl') {
                continue;
            }
            $html .= '<input type="hidden" name="'.$param['name']
                .'" value="'.htmlentities($param['value'])."\" />\n";
        }
        if ($camp->comment->identifier > 0) {
            $html .= "<input type=\"hidden\" name=\"acid\" "
                ."value=\"".$camp->comment->identifier."\" />\n";
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
} // fn smarty_block_comment_form

?>