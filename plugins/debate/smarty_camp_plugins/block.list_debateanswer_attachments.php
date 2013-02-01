<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite debate_list_debateanswer_attachments block plugin
 *
 * Type:     block
 * Name:     list_debateanswer_attachments
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
function smarty_block_list_debateanswer_attachments($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('DebateAnswerAttachmentsList');
    	$debateAnswerAttachmentsList = new DebateAnswerAttachmentsList($start, $p_params);
    	$campContext->setCurrentList($debateAnswerAttachmentsList, array('attachment'));
    }

    $currentDebateAnswerAttachment = $campContext->current_list->current;

    if (is_null($currentDebateAnswerAttachment)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->attachment = $currentDebateAnswerAttachment;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_list->defaultIterator()->next();
    		if (!is_null($campContext->current_list->current)) {
                $campContext->attachment = $campContext->current_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_debate_answers
