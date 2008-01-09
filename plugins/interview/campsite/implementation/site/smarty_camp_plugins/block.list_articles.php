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
    	$start = 0;
    	$articlesList = new ArticlesList($start, $p_params);
    	$campContext->setCurrentList($articlesList, array('publication', 'language',
    	                                                  'issue', 'section', 'article',
    	                                                  'image', 'attachment', 'comment',
    	                                                  'audioclip', 'subtitle'));
    }

    $currentArticle = $campContext->current_articles_list->current;
    if (is_null($currentArticle)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->article = $currentArticle;
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_articles_list->defaultIterator()->next();
    		if (!is_null($campContext->current_articles_list->current)) {
    		    $campContext->article = $campContext->current_articles_list->current;
    		}
    	}
    }

    return $html;
}

?>