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
    $campsite = $p_smarty->get_template_vars('campsite');
    if ($campsite->issue->defined
            && $campsite->issue->number == $p_params['number']) {
        return;
    }

    $issue = new MetaIssue($campsite->publication->identifier,
                           $campsite->language->number, $p_params['number']);
    if ($issue->defined == 'defined') {
        $campsite->issue = $issue;
    }

} // fn smarty_function_set_issue

?>