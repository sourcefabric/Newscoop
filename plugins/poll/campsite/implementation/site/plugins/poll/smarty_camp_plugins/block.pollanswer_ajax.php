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
function smarty_block_pollanswer_ajax($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';
    
    $poll_nr = $campsite->poll->number;
    $answer_nr = $campsite->pollanswer->number;
    $id = "f_pollanswer_{$poll_nr}_{$answer_nr}";
    
    if (isset($p_content)) {
    	$html .= "<span onClick=\"$('{$id}').checked=true; poll_{$campsite->poll->identifier}_vote()\" style=\"cursor: pointer\" >\n";
        $html .= "<input type=\"radio\" id=\"{$id}\" name=\"f_pollanswer_nr\" value=\"{$answer_nr}\" style=\"display: none\" />\n"; 
        $html .= $p_content;        
        $html .= "</span>\n";
    }

    return $html;
} // fn smarty_block_poll_form

?>