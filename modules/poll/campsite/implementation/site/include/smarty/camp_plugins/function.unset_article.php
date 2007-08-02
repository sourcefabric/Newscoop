<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_article function plugin
 *
 * Type:     function
 * Name:     unset_article
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_article($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->article) || !$campsite->article->defined) {
        return;
    }

    $campsite->article = new MetaArticle();
} // fn smarty_function_unset_article

?>