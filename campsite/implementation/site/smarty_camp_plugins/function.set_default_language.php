<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_default_language function plugin
 *
 * Type:     function
 * Name:     set_language
 * Purpose:
 *
 * @param array
 *     $p_params The English name of the language to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_default_language($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $campsite->language = $campsite->default_language;
} // fn smarty_function_set_default_language

?>