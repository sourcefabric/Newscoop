<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_topic function plugin
 *
 * Type:     function
 * Name:     unset_topic
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_topic($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!is_object($campsite->topic) || !$campsite->topic->defined) {
        return;
    }

    $campsite->topic = new MetaTopic();

} // fn smarty_function_unset_topic

?>