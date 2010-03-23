<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_article_comment function plugin
 *
 * Type:     function
 * Name:     unset_article_comment
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_comment($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->comment)
            || !$campsite->comment->defined) {
        return;
    }

    $campsite->comment = new MetaComment();

} // fn smarty_function_unset_article_comment

?>