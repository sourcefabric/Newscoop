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
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('IssuesList');
    	$issuesList = new IssuesList($start, $p_params);
    	if ($issuesList->isEmpty()) {
    	    $p_repeat = false;
    	    return null;
    	}
    	$campContext->setCurrentList($issuesList, array('publication', 'language',
    	                                                'issue', 'section', 'article',
    	                                                'image', 'attachment', 'comment',
    	                                                'audioclip', 'subtitle'));
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