<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite poll_list_answers block plugin
 *
 * Type:     block
 * Name:     poll_list
 * Purpose:  Create a list answers to one poll
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
function smarty_block_list_poll_answers($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('PollAnswersList');
    	$pollAnswersList = new PollAnswersList($start, $p_params);
    	$campContext->setCurrentList($pollAnswersList, array('pollanswer'));
    }

    $currentPollAnswer = $campContext->current_pollanswers_list->current;
    
    if (is_null($currentPollAnswer)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->pollanswer = $currentPollAnswer;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_pollanswers_list->defaultIterator()->next();
    		if (!is_null($campContext->current_pollanswers_list->current)) {
                $campContext->pollanswer = $campContext->current_pollanswers_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_poll_answers

?>
