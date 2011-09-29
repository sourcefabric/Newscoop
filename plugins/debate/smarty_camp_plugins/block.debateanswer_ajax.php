<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite debateanswer_ajax block plugin
 *
 * Type:     block
 * Name:     debateanswer_ajax
 * Purpose:  Provides ajax code for an debate answer
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
function smarty_block_debateanswer_ajax($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $campsite = $p_smarty->loadPlugin('gimme');
    $html = '';

    // the value for this rating
    if (settype($p_params['value'], 'integer') && $p_params['value'] != 0) {
        $value = $p_params['value'];
    } else {
        $value = 1;
    }

    $debate_nr = $campsite->debate->number;
    $language_id = $campsite->debate->language_id;
    $answer_nr = $campsite->debateanswer->number;
    $id = "f_debateanswer_{$debate_nr}_{$language_id}_{$answer_nr}_{$value}";

    // store the allowed values to session for verifying the voting action
    $_SESSION['camp_debate_maxvote'][$debate_nr][$language_id][$answer_nr][$value] = true;

    if (isset($p_content)) {

        if ($campsite->debate->is_votable) {
    	   $html .= "<span onClick=\"$('{$id}').checked=true; debate_{$campsite->debate->identifier}_vote()\" style=\"cursor: pointer\" >\n";
        }

        $html .= "<input type=\"radio\" id=\"{$id}\" name=\"f_debateanswer_{$answer_nr}\" value=\"{$value}\" style=\"display: none\" />\n";
        $html .= $p_content;
        $html .= "</span>\n";
    }

    return $html;
} // fn smarty_block_debate_form

