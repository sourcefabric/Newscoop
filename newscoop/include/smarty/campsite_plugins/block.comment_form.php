<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite comment_form block plugin
 *
 * Type:     block
 * Name:     comment_form
 * Purpose:  Displays a form for comment input
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 string|false
 */
function smarty_block_comment_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    if (!isset($p_content)) {
        return '';
    }

    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    $campsite = $p_smarty->getTemplateVars('gimme');

    if (!$campsite->article->comments_enabled) {
        return '';
    }

    $url = $campsite->url;
    $url->uri_parameter = "";
    $template = null;
    if (isset($p_params['template'])) {
        $themePath = $campsite->issue->defined() ? $campsite->issue->theme_path : $campsite->publication->theme_path;
        $template = new MetaTemplate($p_params['template'], $themePath);
        if (!$template->defined()) {
            CampTemplate::singleton()->trigger_error('invalid template "' . $p_params['template']
            . '" specified in the comment form');
            return false;
        }
    } elseif (is_numeric($url->get_parameter('tpl'))) {
        $template = $campsite->default_template;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    $anchor = isset($p_params['anchor']) ? '#'.$p_params['anchor'] : null;
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }
    if (!isset($p_params['button_html_code']) || empty($p_params['button_html_code'])) {
        $p_params['button_html_code'] = '';
    }

    if (isset($template)) {
        $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
    }
    $html = "<form name=\"submit_comment\" action=\"" . $url->uri_path . "$anchor\" "
    . "method=\"post\" " . $p_params['html_code'] . ">\n";
    if (isset($template)) {
        $html .= "<input type=\"hidden\" name=\"tpl\" value=\"" . $template->identifier . "\" />\n";
    }
    foreach ($campsite->url->form_parameters as $param) {
        if ($param['name'] == 'tpl') {
            continue;
        }
        $html .= '<input type="hidden" name="'.$param['name']
        .'" value="'.htmlentities($param['value'])."\" />\n";
    }
    if ($campsite->comment->identifier > 0) {
        $html .= "<input type=\"hidden\" name=\"acid\" "
        ."value=\"".$campsite->comment->identifier."\" />\n";
    }
    $html .= $p_content;
    $html .=    '<span style="display:none;visibility:hidden;">
                <label for="f_comment_email_protect">
                Ignore this text box. It is used to detect spammers. 
                If you enter anything into this text box, your message 
                will not be sent.
                </label>
                <input type="text" name="f_comment_email_protect" size="20" value="" />
                </span>';
    $html .= "<input type=\"submit\" name=\"f_submit_comment\" "
    ."id=\"article_comment_submit\" value=\""
    .smarty_function_escape_special_chars($p_params['submit_button'])
    ."\" " . $p_params['button_html_code'] . " />\n";
    if (isset($p_params['preview_button']) && !empty($p_params['preview_button'])) {
        $html .= "<input type=\"submit\" name=\"f_preview_comment\" "
        ."id=\"article_comment_preview\" value=\""
        .smarty_function_escape_special_chars($p_params['preview_button'])
        ."\" " . $p_params['button_html_code'] . " />\n";
    }
    $html .= "</form>\n";

    return $html;
} // fn smarty_block_comment_form

?>
