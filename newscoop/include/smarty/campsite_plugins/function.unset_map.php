<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_map function plugin
 *
 * Type:     function
 * Name:     unset_map
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_map($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('gimme');

    $campsite->map_dynamic = null;
} // fn smarty_function_unset_map

?>
