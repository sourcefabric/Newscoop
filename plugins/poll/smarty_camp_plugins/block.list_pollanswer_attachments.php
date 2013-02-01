<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite poll_list_pollanswer_attachments block plugin
 *
 * Type:     block
 * Name:     list_pollanswer_attachments
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
function smarty_block_list_pollanswer_attachments($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('PollAnswerAttachmentsList');
    	$pollAnswerAttachmentsList = new PollAnswerAttachmentsList($start, $p_params);
    	$campContext->setCurrentList($pollAnswerAttachmentsList, array('attachment'));
    }

    $currentPollAnswerAttachment = $campContext->current_attachments_list->current;
    
    if (is_null($currentPollAnswerAttachment)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->attachment = $currentPollAnswerAttachment;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_attachments_list->defaultIterator()->next();
    		if (!is_null($campContext->current_attachments_list->current)) {
                $campContext->attachment = $campContext->current_attachments_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_poll_answers

?>
