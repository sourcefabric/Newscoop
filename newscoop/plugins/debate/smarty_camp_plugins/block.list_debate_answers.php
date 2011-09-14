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
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('gimme');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('DebateAnswersList');
    	$debateAnswersList = new DebateAnswersList($start, $p_params);
    	$campContext->setCurrentList($debateAnswersList, array('debateanswer'));
    }

    $currentDebateAnswer = $campContext->current_list->current;

    if (is_null($currentDebateAnswer)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->debateanswer = $currentDebateAnswer;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_list->defaultIterator()->next();
    		if (!is_null($campContext->current_list->current)) {
                $campContext->debateanswer = $campContext->current_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_debate_answers

