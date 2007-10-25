<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_issue function plugin
 *
 * Type:     function
 * Name:     unset_issue
 * Purpose:  
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_issue($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->issue) || !$campsite->issue->defined) {
        return;
    }

    $campsite->issue = new MetaIssue();

} // fn smarty_function_unset_issue

?>