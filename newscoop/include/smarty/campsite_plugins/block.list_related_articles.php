<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite list_related_articles block plugin
 *
 * Type:     block
 * Name:     list_related_articles
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
function smarty_block_list_related_articles($p_params, $p_content, &$p_smarty, &$p_repeat)
{
	$p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
	$campContext = $p_smarty->getTemplateVars('gimme');

	// Default to true, but not when previewing
	if (!isset($p_params['published']) && !$campContext->preview) {
		$p_params['published'] = 'true';
	}

	if (!isset($p_content)) {
		$start = $campContext->next_list_start('BoxArticlesList');
		$boxArticlesList = new BoxArticlesList($start, $p_params);
		if ($boxArticlesList->isEmpty()) {
			$campContext->setCurrentList($boxArticlesList, array());
			$campContext->resetCurrentList();
			$p_repeat = false;
			return null;
		}
		$campContext->setCurrentList($boxArticlesList, array('publication', 'language',
    	                                                'issue', 'section', 'article',
    	                                                'image', 'attachment', 'comment',
    	                                                'subtitle'));
		$campContext->article = $campContext->current_list->current;
		$p_repeat = true;
	} else {
		$campContext->current_list->defaultIterator()->next();
		if (!is_null($campContext->current_list->current)) {
			$campContext->article = $campContext->current_list->current;
			$p_repeat = true;
		} else {
			$campContext->resetCurrentList();
			$p_repeat = false;
		}
	}

	return $p_content;
}

?>
