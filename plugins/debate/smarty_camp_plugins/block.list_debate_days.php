<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite debate_list_answers block plugin
 *
 * Type:     block
 * Name:     debate_list
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
function smarty_block_list_debate_days($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    /* @var $campContext CampContext */
    $html = '';

    if (!isset($p_content))
    {
        $start = $campContext->next_list_start('DebateVotesList');
    	$debateDaysList = new DebateDaysList($start, $p_params);
    	$campContext->setCurrentList($debateDaysList, array('debatedays'));
    }

    $currentDay = $campContext->current_list->current;

    if (is_null($currentDay)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->debatedays = $currentDay;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_list->defaultIterator()->next();
    		if (!is_null($campContext->current_list->current)) {
                $campContext->debatedays = $campContext->current_list->current;
            }
    	}
    }

    return $html;
} // fn smarty_block_list_debate_answers

?>