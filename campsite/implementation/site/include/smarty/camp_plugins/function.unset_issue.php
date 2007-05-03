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
    $camp = $p_smarty->get_template_vars('camp');
    if (!is_object($camp->issue) || !$camp->issue->defined) {
        return;
    }

    unset($camp->issue);
    $p_smarty->assign('issue', null);
} // fn smarty_function_unset_issue

?>