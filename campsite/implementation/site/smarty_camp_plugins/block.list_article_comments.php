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
        $campContext->setCurrentList($articleCommentsList, array('comment'));
    }

    $currentArticleComment = $campContext->current_article_comments_list->defaultIterator()->current();
    if (is_null($currentArticleComment)) {
        $campContext->url->reset_parameter('acid');
        $p_repeat = false;
        $campContext->resetCurrentList();
        return $html;
    } else {
        $p_repeat = true;
        $campContext->comment = $currentArticleComment;
        $campContext->url->set_parameter('acid', $campContext->comment->identifier);
    }

    if (isset($p_content)) {
        $html = $p_content;
        if ($p_repeat) {
            $campContext->current_article_comments_list->defaultIterator()->next();
            if (!is_null($campContext->current_article_comments_list->current)) {
                $campContext->comment = $campContext->current_article_comments_list->current;
                $campContext->url->set_parameter('acid', $campContext->comment->identifier);
            }
        }
    }

    return $html;
}

?>