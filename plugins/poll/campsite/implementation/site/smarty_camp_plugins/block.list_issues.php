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
    $html = '';

    if (!isset($p_content)) {
    	$start = 0;
    	$issuesList = new IssuesList($start, $p_params);
    	$campContext->setCurrentList($issuesList, array('publication', 'language',
    	                                                'issue', 'section', 'article',
    	                                                'image', 'attachment', 'comment',
    	                                                'audioclip', 'subtitle'));
    }

    $currentIssue = $campContext->current_issues_list->current;
    if (is_null($currentIssue)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$campContext->issue = $currentIssue;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_issues_list->defaultIterator()->next();
    		if (!is_null($campContext->current_issues_list->current)) {
    		    $campContext->issue = $campContext->current_issues_list->current;
    		}
    	}
    }

    return $html;
}

?>