<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_publication function plugin
 *
 * Type:     function
 * Name:     unset_publication
 * Purpose:  
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_publication($p_params, &$p_smarty)
{
    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    if (!is_object($camp->publication) || !$camp->publication->defined) {
        return;
    }

    unset($camp->publication);
    $p_smarty->assign('publication', null);
} // fn smarty_function_unset_publication

?>