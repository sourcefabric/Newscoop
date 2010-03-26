<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_default_issue function plugin
 *
 * Type:     function
 * Name:     set_default_issue
 * Purpose:
 *
 * @param array
 *     $p_params The number of the issue to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_default_issue($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $campsite->issue = $campsite->default_issue;
} // fn smarty_function_set_default_issue

?>