<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite interview_list block plugin
 *
 * Type:     block
 * Name:     interview_list
 * Purpose:  Create a list of available interviews
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
function smarty_block_list_interviews($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = 0;
    	$interviewsList = new InterviewsList($start, $p_params);
    	$campContext->setCurrentList($interviewsList);
    
    	if ($p_params['debug']) {
        	echo "<p>start: " . $campContext->current_interviews_list->getStart()
        	    . ", item: " . $campContext->current_interviews_list->item
        		. ", length: " . $campContext->current_interviews_list->getLength()
        		. ", limit: " . $campContext->current_interviews_list->getLimit()
        		. ", columns: " . $campContext->current_interviews_list->getColumns()
    			. ", has next elements: " . (int)$campContext->current_interviews_list->hasNextElements() . "</p>\n";
        	echo "<p>name: " . $campContext->current_interviews_list->getName() . "</p>\n";
        	echo "<p>constraints: " . $campContext->current_interviews_list->getConstraintsString() . "</p>\n";
        	echo "<p>order: " . $campContext->current_interviews_list->getOrderString() . "</p>\n";
    	}
    }

    $Interview = $campContext->current_interviews_list;
    $currentInterview = $Interview->current;
    if (is_null($currentInterview)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_interviews_list->defaultIterator()->next();
    	}
    }

    return $html;
} // fn smarty_block_list_interviews

?>