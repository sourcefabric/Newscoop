<?php
/**
 * @package Newscoop
 */

/**
 * Newscoop list_article_authors block plugin
 *
 * Type:     block
 * Name:     list_article_authors
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
function smarty_block_list_article_authors($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $context = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_content)) {
        $start = $context->next_list_start('ArticleAuthorsList');
        $articleAuthorsList = new ArticleAuthorsList($start, $p_params);
        if ($articleAuthorsList->isEmpty()) {
            $context->setCurrentList($articleAuthorsList, array());
            $context->resetCurrentList();
        	$p_repeat = false;
            return null;
        }
    	$context->setCurrentList($articleAuthorsList, array('author'));
    	$context->author = $context->current_article_authors_list->current;
    	$p_repeat = true;
    } else {
        $context->current_article_authors_list->defaultIterator()->next();
        if (!is_null($context->current_article_authors_list->current)) {
            $context->author = $context->current_article_authors_list->current;
            $p_repeat = true;
        } else {
            $context->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}
?>
