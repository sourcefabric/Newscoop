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
    $camp = $p_smarty->get_template_vars('camp');
    if (!is_object($camp->article) || !$camp->article->defined) {
        return;
    }

    unset($camp->article);
    $p_smarty->assign('article', null);
} // fn smarty_function_unset_article

?>