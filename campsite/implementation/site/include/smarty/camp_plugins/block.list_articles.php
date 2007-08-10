<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_articles block plugin
 *
 * Type:     block
 * Name:     list_articles
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
function smarty_block_list_articles($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
    	$start = 4;
    	$articleList = new ArticleList($start, $p_params);
    	$campContext->setCurrentList($articleList);
    	echo "<p>start: " . $campContext->current_articles_list->getStart()
    		. ", length: " . $campContext->current_articles_list->getLength()
    		. ", limit: " . $campContext->current_articles_list->getLimit()
    		. ", columns: " . $campContext->current_articles_list->getColumns()
			. ", has next elements: " . (int)$campContext->current_articles_list->hasNextElements() . "</p>\n";
    	echo "<p>name: " . $campContext->current_articles_list->getName() . "</p>\n";
    	echo "<p>constraints: " . $campContext->current_articles_list->getConstraintsString() . "</p>\n";
    	echo "<p>order: " . $campContext->current_articles_list->getOrderString() . "</p>\n";
    }

    $currentArticle = $campContext->current_articles_list->defaultIterator()->current();
    if (is_null($currentArticle)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_articles_list->defaultIterator()->next();
    	}
    }

    return $html;
}

?>