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
 * Purpose:  Provides a form for an poll
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
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_params['template'])) {
        return false;
    }
    
    if (isset($p_params['template'])) {
        $template = new Template($p_params['template']);
        $tpl_id = $template->getTemplateId();
    }

    if (isset($p_content)) {
        $html .= "<form name=\"poll\" action=\"{$campsite->url->uri_path}\" method=\"post\">\n";
        $html .= "<INPUT TYPE=\"hidden\" NAME=\"f_poll_language_id\" VALUE=\"{$campsite->poll->language_id}\" />\n";
        $html .= "<INPUT TYPE=\"hidden\" NAME=\"f_poll_nr\" VALUE=\"{$campsite->poll->number}\" />\n";
        
        if ($tpl_id) {
            $html .= "<input type=\"hidden\" name=\"".TEMPLATE_ID."\" value=\"$tpl_id\" />\n";
        }
        
        $html .= $p_content;
        $html .= "\n</form>\n";
    }

    return $html;
} // fn smarty_block_poll_form

?>