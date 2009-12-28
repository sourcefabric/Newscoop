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
    	$campContext->setCurrentList($interviewsList, array('interview'));
    }

    $currentInterview = $campContext->current_interviews_list->current;
    
    if (is_null($currentInterview)) {
	    $p_repeat = false;
	    $campContext->url->reset_parameter('f_interview_id');
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->interview = $currentInterview;
    	$p_repeat = true;
    	$campContext->url->set_parameter('f_interview_id', $currentInterview->identifier);
    }

    if (isset($p_content)) {
		$html = $p_content;
        if ($p_repeat) {
            $campContext->current_interviews_list->defaultIterator()->next();
            if (!is_null($campContext->current_interviews_list->current)) {
                $campContext->interview = $campContext->current_interviews_list->current;
                $campContext->url->set_parameter('f_interview_id', $campContext->current_interviews_list->current->identifier);
            }
        }
    }

    return $html;
} // fn smarty_block_list_interviews

?>