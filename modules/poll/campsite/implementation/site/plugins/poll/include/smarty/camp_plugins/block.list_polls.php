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
 * Purpose:  Displayes the poll for voting
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
function smarty_block_list_polls($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = 3;
    	$pollsList = new PollsList($start, $p_params);
    	$campContext->setCurrentList($pollsList);
    
    	echo "<p>start: " . $campContext->current_polls_list->getStart()
    		. ", length: " . $campContext->current_polls_list->getLength()
    		. ", limit: " . $campContext->current_polls_list->getLimit()
    		. ", columns: " . $campContext->current_polls_list->getColumns()
			. ", has next elements: " . (int)$campContext->current_polls_list->hasNextElements() . "</p>\n";
    	echo "<p>name: " . $campContext->current_polls_list->getName() . "</p>\n";
    	echo "<p>constraints: " . $campContext->current_polls_list->getConstraintsString() . "</p>\n";
    	echo "<p>order: " . $campContext->current_polls_list->getOrderString() . "</p>\n";
    
    }

    $Poll = $campContext->current_polls_list;
    $currentPoll = $Poll->current;
    if (is_null($currentPoll)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_polls_list->defaultIterator()->next();
    	}
    }

    return $html;
} // fn smarty_block_list_polls

?>