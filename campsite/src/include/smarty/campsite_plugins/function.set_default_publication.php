<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_default_publication function plugin
 *
 * Type:     function
 * Name:     set_default_publication
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the publication to be set
 *     $p_params[identifier] The Identifier of the publication to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_default_publication($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $campsite->publication = $campsite->default_publication;
} // fn smarty_function_set_default_publication

?>