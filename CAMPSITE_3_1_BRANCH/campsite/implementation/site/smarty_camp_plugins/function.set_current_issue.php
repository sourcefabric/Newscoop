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
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $currentIssue = Issue::GetCurrentIssue($campsite->publication->identifier,
    										$campsite->language->number);
    if (is_null($currentIssue)) {
        return false;
    }

    // if the current issue was already the context do nothing
    if ($campsite->issue->defined
            && $campsite->issue->number == $currentIssue->getIssueNumber()) {
        return;
    }

    $issueObj = new MetaIssue($campsite->publication->identifier,
                              $campsite->language->number,
                              $currentIssue->getIssueNumber());
    if ($issueObj->defined) {
        $campsite->issue = $issueObj;
    }
} // fn smarty_function_set_current_issue

?>