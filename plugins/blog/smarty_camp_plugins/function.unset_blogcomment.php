<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_blogcomment function plugin
 *
 * Type:     function
 * Name:     unset_blogcomment
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_blogcomment($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->blogcomment) || !$campsite->blogcomment->defined) {
        return;
    }

    $campsite->blogcomment = new MetaBlogComment();

} // fn smarty_function_unset_blogcomment

?>