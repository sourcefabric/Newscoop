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
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = 0;
    	$pollAnswersList = new PollAnswersList($start, $p_params);
    	$campContext->setCurrentList($pollAnswersList);
    
    	if ($p_params['debug']) {
        	echo "<p>start: " . $campContext->current_pollanswers_list->getStart()
        	    . ", item: " . $campContext->current_pollanswers_list->item
        		. ", length: " . $campContext->current_pollanswers_list->getLength()
        		. ", limit: " . $campContext->current_pollanswers_list->getLimit()
        		. ", columns: " . $campContext->current_pollanswers_list->getColumns()
    			. ", has next elements: " . (int)$campContext->current_pollanswers_list->hasNextElements() . "</p>\n";
        	echo "<p>name: " . $campContext->current_pollanswers_list->getName() . "</p>\n";
        	echo "<p>constraints: " . $campContext->current_pollanswers_list->getConstraintsString() . "</p>\n";
        	echo "<p>order: " . $campContext->current_pollanswers_list->getOrderString() . "</p>\n";
    	}
    }

    $PollAnswer = $campContext->current_pollanswers_list;
    $currentPollAnswer = $PollAnswer->current;
    if (is_null($currentPollAnswer)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_pollanswers_list->defaultIterator()->next();
    	}
    }

    return $html;
} // fn smarty_block_list_poll_answers

?>