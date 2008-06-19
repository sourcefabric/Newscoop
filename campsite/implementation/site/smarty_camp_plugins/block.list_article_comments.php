<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_article_comments block plugin
 *
 * Type:     block
 * Name:     list_article_comments
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
function smarty_block_list_article_comments($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('ArticleCommentsList');
        $articleCommentsList = new ArticleCommentsList($start, $p_params);
        if ($articleCommentsList->isEmpty()) {
            $p_repeat = false;
            return null;
        }
        $campContext->setCurrentList($articleCommentsList, array('comment'));
        $campContext->comment = $campContext->current_article_comments_list->current;
        $p_repeat = true;
    } else {
        $campContext->current_article_comments_list->defaultIterator()->next();
        if (!is_null($campContext->current_article_comments_list->current)) {
            $campContext->comment = $campContext->current_article_comments_list->current;
            $campContext->url->set_parameter('acid', $campContext->comment->identifier);
            $p_repeat = true;
        } else {
            $campContext->url->reset_parameter('acid');
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}

?>