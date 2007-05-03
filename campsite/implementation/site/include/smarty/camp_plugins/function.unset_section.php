<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_section function plugin
 *
 * Type:     function
 * Name:     unset_section
 * Purpose:  
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_section($p_params, &$p_smarty)
{
    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    if (!is_object($camp->section) || !$camp->section->defined) {
        return;
    }

    unset($camp->section);
    $p_smarty->assign('section', null);
} // fn smarty_function_unset_section

?>