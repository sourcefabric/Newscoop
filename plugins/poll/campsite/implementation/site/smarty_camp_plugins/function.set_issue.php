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
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if (isset($p_params['number'])) {
    	$issueNumber = intval($p_params['number']);
    } else {
    	$property = array_shift(array_keys($p_params));
    	$campsite->issue->trigger_invalid_property_error($property, $p_smarty);
        return false;
    }

    if ($campsite->issue->defined
            && $campsite->issue->number == $issueNumber) {
        return;
    }

    $issueObj = new MetaIssue($campsite->publication->identifier,
                              $campsite->language->number, $issueNumber);
    if ($issueObj->defined) {
        $campsite->issue = $issueObj;
    } else {
    	$campsite->issue->trigger_invalid_value_error('number', $p_params['number'], $p_smarty);
    }
} // fn smarty_function_set_issue

?>