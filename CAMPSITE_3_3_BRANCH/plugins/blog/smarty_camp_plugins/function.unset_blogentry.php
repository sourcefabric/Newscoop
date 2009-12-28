<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_blogentry function plugin
 *
 * Type:     function
 * Name:     unset_blogentry
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_blogentry($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->blogentry) || !$campsite->blogentry->defined) {
        return;
    }

    $campsite->blogentry = new MetaBlogEntry();

} // fn smarty_function_unset_blogentry

?>