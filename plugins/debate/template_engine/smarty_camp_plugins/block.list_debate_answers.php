<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite debate_list_answers block plugin
 *
 * Type:     block
 * Name:     debate_list
 * Purpose:  Create a list answers to one debate
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_content
 * @param string
 *     $p_smarty
 * @param string
 *     $p_repeat
 *
 * @return
 *
 */
function smarty_block_list_debate_answers($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('DebateAnswersList');
    	$debateAnswersList = new DebateAnswersList($start, $p_params);
    	if ($debateAnswersList->isEmpty()) {
            $campContext->setCurrentList($debateAnswersList, array());
            $campContext->resetCurrentList();
    		$p_repeat = false;
    	    return null;
    	}
    	$campContext->setCurrentList($debateAnswersList, array('debateanswers'));
    	$campContext->debateanswer = $campContext->current_debateanswers_list->current;
    	$p_repeat = true;
    } else {
        $campContext->current_debateanswers_list->defaultIterator()->next();
        if (!is_null($campContext->current_debateanswers_list->current)) {
            $campContext->debateanswer = $campContext->current_debateanswers_list->current;
            $p_repeat = true;
        } else {
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
} // fn smarty_block_list_debate_answers

