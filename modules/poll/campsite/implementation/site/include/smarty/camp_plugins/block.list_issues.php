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
    	$start = 3;
    	$issuesList = new IssuesList($start, $p_params);
    	$campContext->setCurrentList($issuesList);
    	echo "<p>start: " . $campContext->current_issues_list->getStart()
    		. ", length: " . $campContext->current_issues_list->getLength()
    		. ", limit: " . $campContext->current_issues_list->getLimit()
    		. ", columns: " . $campContext->current_issues_list->getColumns()
			. ", has next elements: " . (int)$campContext->current_issues_list->hasNextElements() . "</p>\n";
    	echo "<p>name: " . $campContext->current_issues_list->getName() . "</p>\n";
    	echo "<p>constraints: " . $campContext->current_issues_list->getConstraintsString() . "</p>\n";
    	echo "<p>order: " . $campContext->current_issues_list->getOrderString() . "</p>\n";
    }

    $currentIssue = $campContext->current_issues_list->defaultIterator()->current();
    if (is_null($currentIssue)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_issues_list->defaultIterator()->next();
    	}
    }

    return $html;
}

?>