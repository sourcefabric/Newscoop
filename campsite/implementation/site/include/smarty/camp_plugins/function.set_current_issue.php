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
    $campsite = $p_smarty->get_template_vars('campsite');

    $queryStr = "SELECT MAX(Number) AS MaxIssueNr FROM Issues "
               ."WHERE IdPublication = " . $campsite->publication->identifier
               ." AND IdLanguage = " . $campsite->language->number
               ." AND Published = 'Y'";
    $row = $g_ado_db->GetRow($queryStr);

    if (!is_array($row) || $row['MaxIssueNr'] < 1) {
        return false; // or trhow an error?
    }
    // if the current issue is already the context, it just return nothing
    if ($campsite->issue->defined
            && $campsite->issue->number == $row['MaxIssueNr']) {
        return;
    }

    $issue = new MetaIssue($campsite->publication->identifier,
                           $campsite->language->number, $row['MaxIssueNr']);
    if ($issue->defined == 'defined') {
        $campsite->issue = $issue;
    }

} // fn smarty_function_set_current_issue

?>