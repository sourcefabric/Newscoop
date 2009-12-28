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

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('ArticlesList');
        $articlesList = new ArticlesList($start, $p_params);
        if ($articlesList->isEmpty()) {
            $campContext->setCurrentList($articlesList, array());
            $campContext->resetCurrentList();
        	$p_repeat = false;
            return null;
        }
    	$campContext->setCurrentList($articlesList, array('publication', 'language',
    	                                                  'issue', 'section', 'article',
    	                                                  'image', 'attachment', 'comment',
    	                                                  'audioclip', 'subtitle'));
    	$campContext->article = $campContext->current_articles_list->current;
    	$p_repeat = true;
    } else {
        $campContext->current_articles_list->defaultIterator()->next();
    	if (!is_null($campContext->current_articles_list->current)) {
    	    $campContext->article = $campContext->current_articles_list->current;
    	    $p_repeat = true;
    	} else {
    	    $campContext->resetCurrentList();
            $p_repeat = false;
    	}
    }

    return $p_content;
}

?>