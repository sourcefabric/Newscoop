<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_issues block plugin
 *
 * Type:     block
 * Name:     list_issues
 * Purpose:  Provides a...
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
function smarty_block_list_issues($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    $campContext = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('IssuesList');
    	$issuesList = new IssuesList($start, $p_params);
    	if ($issuesList->isEmpty()) {
            $campContext->setCurrentList($issuesList, array());
            $campContext->resetCurrentList();
    		$p_repeat = false;
    	    return null;
    	}
    	$campContext->setCurrentList($issuesList, array('publication', 'language',
    	                                                'issue', 'section', 'article',
    	                                                'image', 'attachment', 'comment',
    	                                                'subtitle'));
    	$campContext->issue = $campContext->current_issues_list->current;
    	$p_repeat = true;
    } else {
        $campContext->current_issues_list->defaultIterator()->next();
        if (!is_null($campContext->current_issues_list->current)) {
            $campContext->issue = $campContext->current_issues_list->current;
            $p_repeat = true;
        } else {
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}

?>
