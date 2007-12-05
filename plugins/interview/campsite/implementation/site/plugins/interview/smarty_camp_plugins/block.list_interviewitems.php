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
 * Purpose:  Create a list of available interview_items
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
function smarty_block_list_interviewitems($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = 0;
    	$interviewItemsList = new InterviewItemsList($start, $p_params);
    	$campContext->setCurrentList($interviewItemsList, array('interviewitem'));
    }

    $currentIterviewItem = $campContext->current_interviewitems_list->current;
    if (is_null($currentIterviewItem)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->interviewitem = $currentIterviewItem;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
        if ($p_repeat) {
            $campContext->current_interviewitems_list->defaultIterator()->next();
            if (!is_null($campContext->current_interviewitems_list->current)) {
                $campContext->interviewitem = $campContext->current_interviewitems_list->current;
            }
        }
    }

    return $html;
} // fn smarty_block_list_interview_items

?>