<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_blog function plugin
 *
 * Type:     function
 * Name:     unset_blog
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_blog($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->blog) || !$campsite->blog->defined) {
        return;
    }

    $campsite->blog = new MetaBlog();

} // fn smarty_function_unset_blog

?>