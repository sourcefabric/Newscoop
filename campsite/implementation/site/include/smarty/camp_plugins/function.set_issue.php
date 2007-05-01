<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_issue function plugin
 *
 * Type:     function
 * Name:     set_issue
 * Purpose:  
 *
 * @param array
 *     $p_params The number of the issue to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_issue($p_params, &$p_smarty)
{
    global $g_ado_db;

    if (!isset($p_params['number']) || empty($p_params['number'])) {
        return false;
    }

    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    if ($camp->issue->defined && $camp->issue->number == $p_params['number']) {
        return;
    }

    $issue = new MetaIssue($camp->publication->identifier,
                           $camp->language->number, $p_params['number']);
    if ($issue->defined == 'defined') {
        $camp->issue = $issue;
        $p_smarty->assign('issue', $camp->issue);
    }

} // fn smarty_function_set_issue

?>