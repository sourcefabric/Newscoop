<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_article_attachments block plugin
 *
 * Type:     block
 * Name:     list_article_attachments
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
function smarty_block_list_article_attachments($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
    	$start = 8;
    	$articleAttachmentsList = new ArticleAttachmentsList($start, $p_params);
    	$campContext->setCurrentList($articleAttachmentsList);
    	echo "<p>start: " . $campContext->current_article_attachments_list->getStart()
    		. ", length: " . $campContext->current_article_attachments_list->getLength()
    		. ", limit: " . $campContext->current_article_attachments_list->getLimit()
    		. ", columns: " . $campContext->current_article_attachments_list->getColumns()
			. ", has next elements: " . (int)$campContext->current_article_attachments_list->hasNextElements() . "</p>\n";
    	echo "<p>name: " . $campContext->current_article_attachments_list->getName() . "</p>\n";
    	echo "<p>constraints: " . $campContext->current_article_attachments_list->getConstraintsString() . "</p>\n";
    	echo "<p>order: " . $campContext->current_article_attachments_list->getOrderString() . "</p>\n";
    }

    $currentArticleAttachment = $campContext->current_article_attachments_list->defaultIterator()->current();
    if (is_null($currentArticleAttachment)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_article_attachments_list->defaultIterator()->next();
    	}
    }

    return $html;
}

?>