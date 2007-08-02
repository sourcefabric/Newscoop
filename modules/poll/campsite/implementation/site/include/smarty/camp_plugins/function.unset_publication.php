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
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->publication) || !$campsite->publication->defined) {
        return;
    }

    $campsite->publication = new MetaPublication();

} // fn smarty_function_unset_publication

?>