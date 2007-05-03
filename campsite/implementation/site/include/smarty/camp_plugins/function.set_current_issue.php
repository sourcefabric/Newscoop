<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_current_issue function plugin
 *
 * Type:     function
 * Name:     set_current_issue
 * Purpose:  
 *
 * @param array
 *     $p_params[name] The Name of the publication to be set
 *     $p_params[identifier] The Identifier of the publication to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_current_issue($p_params, &$p_smarty)
{
    global $g_ado_db;

    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');

    $queryStr = "SELECT MAX(Number) AS MaxIssueNr FROM Issues "
               ."WHERE IdPublication = " . $camp->publication->identifier
               ." AND IdLanguage = " . $camp->language->number
               ." AND Published = 'Y'";
    $row = $g_ado_db->GetRow($queryStr);

    if (!is_array($row) || $row['MaxIssueNr'] < 1) {
        return false; // or trhow an error?
    }
    // if the current issue is already the context, it just return nothing
    if ($camp->issue->defined && $camp->issue->number == $row['MaxIssueNr']) {
        return;
    }

    $issue = new MetaIssue($camp->publication->identifier,
                           $camp->language->number, $row['MaxIssueNr']);
    if ($issue->defined == 'defined') {
        $camp->issue = $issue;
        $p_smarty->assign('issue', $camp->issue);
    }

} // fn smarty_function_set_current_issue

?>