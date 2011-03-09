<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_map_articles block plugin
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
function smarty_block_list_map_articles($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('gimme');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('MapArticlesList');
        $mapArticlesList = new MapArticlesList($start, $p_params);
        //echo " xhere 001 ";
        if ($mapArticlesList->isEmpty()) {
            //echo " xhere 002-1 ";
            $campContext->setCurrentList($mapArticlesList, array());
            $campContext->resetCurrentList();
        	$p_repeat = false;
            //echo " xhere 002-2 ";
            return null;
        }
        //echo " xhere 002-b ";
    	//$campContext->setCurrentList($mapArticlesList, array('article'));
/**/
    	$campContext->setCurrentList($mapArticlesList, array('publication', 'language',
    	                                                  'issue', 'section', 'article',
    	                                                  'image', 'attachment', 'comment',
    	                                                  'audioclip', 'subtitle'));
/**/
        //echo " xhere 003 ";
    	$campContext->article = $campContext->current_map_articles_list->current;
        //echo " xhere 004 ";
    	$p_repeat = true;
        //echo " xhere 005 ";
    } else {
        //echo " xhere 006 ";
        $campContext->current_map_articles_list->defaultIterator()->next();
        //echo " xhere 007 ";
    	if (!is_null($campContext->current_map_articles_list->current)) {
    	    $campContext->article = $campContext->current_map_articles_list->current;
            //var_dump($campContext->article);
    	    $p_repeat = true;
    	} else {
    	    $campContext->resetCurrentList();
            $p_repeat = false;
    	}
        //echo " xhere 008 ";
    }

    //echo " xhere 010 ";
    return $p_content;
}

?>
