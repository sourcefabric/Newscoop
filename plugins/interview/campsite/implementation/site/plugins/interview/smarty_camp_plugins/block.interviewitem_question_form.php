<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite interviewitem_question_form block plugin
 *
 * Type:     block
 * Name:     interviewitem_question_form
 * Purpose:  Provides a form for an interview question
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
function smarty_block_interviewitem_question_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';
    
    if (isset($p_params['template'])) {
        $template = new Template($p_params['template']);
        $tpl_id = $template->getTemplateId();
    }
    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }
    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }

    if (isset($p_content)) {
        $html = "<form name=\"interviewitem_question\" action=\"{$campsite->url->uri_path}\" method=\"post\" >\n";
        $html .= "<input type=\"hidden\" name=\"f_interviewitem\" value=\"question\">\n";
        
        if ($tpl_id) {
            $html .= "<input type=\"hidden\" name=\"".TEMPLATE_ID."\" value=\"$tpl_id\" />\n";
        }
        if ($campsite->interview->identifier) {
            $html .= "<input type=\"hidden\" name=\"f_interviewitem_interview_id\" value=\"{$campsite->interview->identifier}\" />\n";
        }
        if ($campsite->interviewitem->identifier) {
            $html .= "<input type=\"hidden\" name=\"f_interviewitem_id\" value=\"{$campsite->interviewitem->identifier}\" />\n";
        }
        $html .= $p_content;
        $html .= "<input type=\"submit\" name=\"f_interviewitem_submit\" value=\""
              .smarty_function_escape_special_chars($p_params['submit_button'])
              ."\" ".$p_params['html_code']." />\n</form>\n";
    }

    return $html;
} // fn smarty_block_poll_form

?>