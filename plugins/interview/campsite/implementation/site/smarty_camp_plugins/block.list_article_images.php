<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_article_images block plugin
 *
 * Type:     block
 * Name:     list_article_images
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
function smarty_block_list_article_images($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('ArticleImagesList');
        $articleImagesList = new ArticleImagesList($start, $p_params);
    	$campContext->setCurrentList($articleImagesList, array('image'));
    }

    $currentArticleImage = $campContext->current_article_images_list->defaultIterator()->current();
    if (is_null($currentArticleImage)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    	$campContext->image = $currentArticleImage;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_article_images_list->defaultIterator()->next();
    		if (!is_null($campContext->current_article_images_list->current)) {
    		    $campContext->image = $campContext->current_article_images_list->current;
    		}
    	}
    }

    return $html;
}

?>