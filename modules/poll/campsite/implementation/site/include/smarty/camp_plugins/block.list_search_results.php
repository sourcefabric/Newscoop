<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_search_results block plugin
 *
 * Type:     block
 * Name:     list_search_results
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
function smarty_block_list_search_results($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
    	$start = 3;
    	$searchResultsList = new SearchResultsList($start, $p_params);
    	$campContext->setCurrentList($searchResultsList);
    	echo "<p>start: " . $campContext->current_search_results_list->getStart()
    		. ", length: " . $campContext->current_search_results_list->getLength()
    		. ", limit: " . $campContext->current_search_results_list->getLimit()
    		. ", columns: " . $campContext->current_search_results_list->getColumns()
			. ", has next elements: " . (int)$campContext->current_search_results_list->hasNextElements() . "</p>\n";
    	echo "<p>name: " . $campContext->current_search_results_list->getName() . "</p>\n";
    	echo "<p>constraints: " . $campContext->current_search_results_list->getConstraintsString() . "</p>\n";
    	echo "<p>order: " . $campContext->current_search_results_list->getOrderString() . "</p>\n";
    }

    $currentSearchResult = $campContext->current_search_results_list->defaultIterator()->current();
    if (is_null($currentSearchResult)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_search_results_list->defaultIterator()->next();
    	}
    }

    return $html;
}

?>