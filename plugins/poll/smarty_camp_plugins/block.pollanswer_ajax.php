<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite pollanswer_ajax block plugin
 *
 * Type:     block
 * Name:     pollanswer_ajax
 * Purpose:  Provides ajax code for an poll answer
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
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');
    $html = '';
    
    // the value for this rating
    if (settype($p_params['value'], 'integer') && $p_params['value'] != 0) {
        $value = $p_params['value'];    
    } else {
        $value = 1;   
    }
    
    $poll_nr = $campsite->poll->number;
    $language_id = $campsite->poll->language_id;
    $answer_nr = $campsite->pollanswer->number;
    $id = "f_pollanswer_{$poll_nr}_{$language_id}_{$answer_nr}_{$value}";
    
    // store the allowed values to session for verifying the voting action
    $_SESSION['camp_poll_maxvote'][$poll_nr][$language_id][$answer_nr][$value] = true; 
    
    if (isset($p_content)) {
        
        if ($campsite->poll->is_votable) {
    	   $html .= "<span onClick=\"$('{$id}').checked=true; poll_{$campsite->poll->identifier}_vote()\" style=\"cursor: pointer\" >\n";
        }
        
        $html .= "<input type=\"radio\" id=\"{$id}\" name=\"f_pollanswer_{$answer_nr}\" value=\"{$value}\" style=\"display: none\" />\n"; 
        $html .= $p_content;        
        $html .= "</span>\n";
    }

    return $html;
} // fn smarty_block_poll_form

?>