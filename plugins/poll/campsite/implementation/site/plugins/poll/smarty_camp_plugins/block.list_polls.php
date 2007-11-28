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
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = 0;
    	$pollsList = new PollsList($start, $p_params);
    	$campContext->setCurrentList($pollsList, array('poll'));
    }

    $currentPoll = $campContext->current_polls_list->current;
    
    if (is_null($currentPoll)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->poll = $currentPoll;
    	$p_repeat = true;
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