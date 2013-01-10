<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite debate_list block plugin
 *
 * Type:     block
 * Name:     debate_list
 * Purpose:  Create a list of available debates
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_content
 * @param Smarty_Internal_Template
 *     $p_smarty
 * @param string
 *     $p_repeat
 *
 * @return
 *
 */
function smarty_block_list_debates($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    /* @var $campContext \CampContext */
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('DebatesList');
    	$debatesList = new DebateList($start, $p_params);
    	$campContext->setCurrentList($debatesList, array('debate'));
    }

    $currentDebate = $campContext->current_list->current;

    if (is_null($currentDebate)) {
	    $p_repeat = false;
	    $campContext->url->reset_parameter('f_debate_nr');
	    $campContext->url->reset_parameter('f_debate_language_id');
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->debate = $currentDebate;
    	$p_repeat = true;
    	$campContext->url->set_parameter('f_debate_nr', $currentDebate->number);
    	$campContext->url->set_parameter('f_debate_language_id', $currentDebate->language_id);
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_list->defaultIterator()->next();
    		if (!is_null($campContext->current_list->current)) {
                $campContext->debate = $campContext->current_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_debates
