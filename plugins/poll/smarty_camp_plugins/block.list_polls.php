<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite poll_list block plugin
 *
 * Type:     block
 * Name:     poll_list
 * Purpose:  Create a list of available polls
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
function smarty_block_list_polls($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('PollsList');
    	$pollsList = new PollsList($start, $p_params);
    	$campContext->setCurrentList($pollsList, array('poll'));
    }

    $currentPoll = $campContext->current_polls_list->current;
    
    if (is_null($currentPoll)) {
	    $p_repeat = false;
	    $campContext->url->reset_parameter('f_poll_nr');
	    $campContext->url->reset_parameter('f_poll_language_id');
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->poll = $currentPoll;
    	$p_repeat = true;
    	$campContext->url->set_parameter('f_poll_nr', $currentPoll->number);
    	$campContext->url->set_parameter('f_poll_language_id', $currentPoll->language_id);
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_polls_list->defaultIterator()->next();
    		if (!is_null($campContext->current_polls_list->current)) {
                $campContext->poll = $campContext->current_polls_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_polls

?>
