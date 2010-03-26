<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_default_section function plugin
 *
 * Type:     function
 * Name:     set_default_section
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the section to be set
 *     $p_params[number] The Number of the section to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_default_section($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $campsite->section = $campsite->default_section;
} // fn smarty_function_set_default_section

?>