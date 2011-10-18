<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite interview_form block plugin
 *
 * Type:     block
 * Name:     interview_form
 * Purpose:  Provides a form for an interview
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
function smarty_block_interview_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('gimme');
    $html = '';

    if (0) {
        $Interview = new Interview($campsite->interview->identifier);
        return $Interview->getForm('/en/first/interview/', '', true);
    }

    if (isset($p_params['template'])) {
            $themePath = $campsite->issue->defined() ? $campsite->issue->theme_path : $campsite->publication->theme_path;
        $template = new MetaTemplate($p_params['template'], $themePath);
        if (!$template->defined()) {
            CampTemplate::singleton()->trigger_error('invalid template "' . $p_params['template']
            . '" specified in the search form');
            return false;
        }
    } elseif (is_numeric($url->get_parameter('tpl'))) {
        $template = $campsite->default_template;
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    if (isset($p_content)) {
        if (isset($template)) {
            $url->uri_parameter = "template " . str_replace(' ', "\\ ", $template->name);
        }
        $html = "<form name=\"interview\" action=\"{$campsite->url->uri_path}\" method=\"post\" enctype=\"multipart/form-data\">\n";
        $html .= "<input type=\"hidden\" name=\"_qf__interview\">\n";
        $html .= "<input type=\"hidden\" name=\"f_interview\" value=\"edit\">\n";

        if (isset($template)) {
            $html .= "<input type=\"hidden\" name=\"tpl\" value=\"" . $template->identifier . "\" />\n";
        }
        if ($campsite->interview->identifier) {
            $html .= "<input type=\"hidden\" name=\"f_interview_id\" value=\"{$campsite->interview->identifier}\" />\n";
        }
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"f_interview_submit\" value=\""
              .smarty_function_escape_special_chars($p_params['submit_button'])
              ."\" ".$p_params['html_code']." />\n</form>\n";
    }

    return $html;
} // fn smarty_block_poll_form

?>